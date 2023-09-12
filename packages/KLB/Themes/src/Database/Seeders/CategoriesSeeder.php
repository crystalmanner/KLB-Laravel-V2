<?php

namespace KLB\Themes\Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Webkul\Category\Models\CategoryTranslationProxy;
use Webkul\Category\Repositories\CategoryRepository;

class CategoriesSeeder extends CSVSeeder
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    private $model;
    private $categoryCache = [];

    public function __construct()
    {
        $this->csvPath = 'data/categories-nested.csv';
        $this->categoryRepository = app(CategoryRepository::class);

        // Fix for PDOException: SQLSTATE[HY000]: General error: 1267 Illegal mix of collations (utf8mb4_unicode_ci,IMPLICIT) and (utf8mb4_0900_ai_ci,IMPLICIT) for operation '=' in KLB-Laravel-V2/vendor/doctrine/dbal/lib/Doctrine/DBAL/Driver/PDOStatement.php:117
        DB::statement('ALTER TABLE categories CONVERT TO CHARACTER SET utf8');
        DB::statement('ALTER TABLE category_translations CONVERT TO CHARACTER SET utf8');
    }

    private function hasParentCategoryConfiguration($model = false)
    {
        $model = $model ? $model : $this->model;

        return array_key_exists('parentSlug', $model) && array_key_exists('parent', $model) && array_key_exists('tier', $model);
    }

    public function sort($model, $key)
    {
        if ($this->hasParentCategoryConfiguration($model)) {
            // If the category CSV has the necessary columns, sort by the tier.
            // This should result in all first tiers being created first,
            // followed then by the second and third tier categories.
            return $model['tier'];
        }

        return parent::sort($model, $key);
    }

    public function create($model)
    {
        $this->model = $model;

        try {
            $this->setInitialData();
            $this->handleParentCategory();

            return $this->createCategoryIfNew();
        } catch (\Exception $e) {
            Log::error($e);

            return false;
        }
    }

    private function setInitialData()
    {
        $this->model['url_path'] = '/' . $this->model['slug'];
    }

    private function createCategoryIfNew()
    {
        if (!$this->categoryExists($this->model['slug'])) {
            $model = $this->removeUnnecessaryData($this->model);
            $category = $this->categoryRepository->create($model);
            return $category;
        }

        return false;
    }

    private function categoryBySlugQuery($slug = false)
    {
        $slug = $slug ? $slug : $this->model['slug'];

        return CategoryTranslationProxy::modelClass()::where('slug', $slug);
    }

    private function categoryExists($slug = false)
    {
        return $this->categoryBySlugQuery($slug)->exists();
    }

    private function removeUnnecessaryData(array $model)
    {
        return collect($model)->reject(function ($value, $key) {
            return in_array($key, [
                'parentSlug',
                'parent',
                'tier',
            ]);
        })->toArray();
    }

    private function hasParentCategory()
    {
        return $this->hasParentCategoryConfiguration() && $this->model['tier'] >= 1;
    }

    private function getCategoryBySlug($slug)
    {
        if (array_key_exists($slug, $this->categoryCache)) {
            return $this->categoryCache[$slug];
        }

        $category = $this->categoryBySlugQuery($slug)->first();
        $this->categoryCache[$slug] = $category;

        return $category;
    }

    private function getParentCategory()
    {
        return $this->getCategoryBySlug($this->model['parentSlug']);
    }

    private function parentCategoryExists()
    {
        return $this->categoryExists($this->model['parentSlug']);
    }

    private function handleParentCategory()
    {
        if ($this->hasParentCategory()) {
            if ($this->parentCategoryExists()) {
                $parentCategory = $this->getParentCategory();
                // Set the parent id on the category for seeding.
                $this->model['parent_id'] = $parentCategory->category_id;
                $this->model['url_path'] = $parentCategory->url_path . $this->model['url_path'];
            } else {
                $slug = $this->model['slug'];
                $parentSlug = $this->model['parentSlug'];
                throw new \Exception("Parent Category ($parentSlug) does not exist for $slug");
            }
        }
    }
}
