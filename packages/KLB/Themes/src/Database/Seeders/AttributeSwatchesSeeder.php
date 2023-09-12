<?php

namespace KLB\Themes\Database\Seeders;

use Illuminate\Support\Facades\Log;
use Webkul\Attribute\Repositories\AttributeFamilyRepository;
use Webkul\Attribute\Repositories\AttributeRepository;

class AttributeSwatchesSeeder extends CSVSeeder
{
    /**
     * @var AttributeRepository
     */
    private $attributeRepository;

    /**
     * @var AttributeFamilyRepository
     */
    private $attributeFamilyRepository;

    private $model;
    private $code;
    private $attribute;
    private $attributesCache = [];
    private $attributesOptionsCache = [];

    public function __construct()
    {
        $this->csvPath = 'data/attributes.csv';
        $this->attributeRepository = app(AttributeRepository::class);
        $this->attributeFamilyRepository = app(AttributeFamilyRepository::class);
    }

    public function create($model)
    {
        $this->model = $model;

        try {
            $this->getAttribute();

            return $this->createSwatchIfNew();
        } catch (\Exception $e) {
            Log::error($e);

            return false;
        }
    }

    private function attributeExists()
    {
        $this->code = $this->model['attribute'];
        if (array_key_exists($this->code, $this->attributesCache)) {
            return true;
        } else {
            $attribute = $this->attributeRepository->getAttributeByCode($this->code);
            if ($attribute) {
                $this->attributesCache[$this->code] = $attribute;
                return true;
            }

            return false;
        }
    }

    private function getAttribute()
    {
        if ($this->attributeExists()) {
            $this->attribute = $this->attributesCache[$this->code];
        } else {
            throw new \Exception("Failed adding option to $this->code. Attribute does not exist.");
        }
    }

    private function swatchExists()
    {
        if (array_key_exists($this->code, $this->attributesOptionsCache)) {
            $swatch = $this->model['value'];
            return $this->attributesOptionsCache[$this->code]->where('admin_name', $swatch)->count() > 0;
        }

        $this->attributesOptionsCache[$this->code] = $this->attribute->options;
        return $this->swatchExists();
    }

    private function createSwatchIfNew()
    {
        $this->getAttribute();
        if (!$this->swatchExists()) {
            $value = $this->model['value'];
            $swatch = $this->attribute->options()->create([
                'admin_name'    => $value,
                'swatch_value'  => $value,
                'sort_order'    => 1,
                'attribute_id'  => $this->attribute->id,
            ]);
            $swatch->translations()->create([
                'locale'                => 'en',
                'label'                 => $value,
                'attribute_option_id'   => $swatch->id,
            ]);

            $this->attributesOptionsCache[$this->code] = $this->attribute->options()->get();

            return $swatch;
        }

        return false;
    }
}
