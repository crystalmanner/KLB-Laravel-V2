<?php

namespace Webkul\Velocity\Repositories\Product;

use Webkul\Core\Eloquent\Repository;
use Illuminate\Container\Container as App;
use Webkul\Product\Models\ProductAttributeValue;
use Webkul\Product\Repositories\ProductFlatRepository;
use Webkul\Attribute\Repositories\AttributeRepository;
//
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\OrderItemRepository;
//
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductRepository extends Repository
{
     /**
     * AttributeRepository object
     *
     * @var \Webkul\Attribute\Repositories\AttributeRepository
     */
    protected $attributeRepository;
    /**
     * OrderRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
     */
    protected $orderRepository;

    /**
     * OrderItemRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderItemRepository
     */
    protected $orderItemRepository;
    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Attribute\Repositories\AttributeRepository  $attributeRepository
     * @param  \Illuminate\Container\Container  $app
     * @return void
     */
    public function __construct(
        OrderRepository $orderRepository,
        OrderItemRepository $orderItemRepository,
        AttributeRepository $attributeRepository,
        App $app
    )
    {
        $this->attributeRepository = $attributeRepository;

        parent::__construct($app);
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return 'Webkul\Product\Contracts\Product';
    }

    /**
     * Returns featured product
     *
     * @param  int  $count
     * @return \Illuminate\Support\Collection
     */
    public function getFeaturedProducts($count)
    {
        $results = app(ProductFlatRepository::class)->scopeQuery(function($query) {
            $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

            $locale = request()->get('locale') ?: app()->getLocale();

            return $query->distinct()
                         ->addSelect('product_flat.*')
                         ->where('product_flat.status', 1)
                         ->where('product_flat.visible_individually', 1)
                         ->where('product_flat.featured', 1)
                         ->where('product_flat.channel', $channel)
                         ->where('product_flat.locale', $locale)
                         ->orderBy('product_id', 'desc');
        })->paginate($count);

        return $results;
    }

    /**
     * Returns newly added product
     *
     * @param  int  $count
     * @return \Illuminate\Support\Collection
     */
    public function getNewProducts($count)
    {
        $results = app(ProductFlatRepository::class)->scopeQuery(function($query) {
            $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

            $locale = request()->get('locale') ?: app()->getLocale();

            return $query->distinct()
                         ->addSelect('product_flat.*')
                         ->where('product_flat.status', 1)
                         ->where('product_flat.visible_individually', 1)
                         ->where('product_flat.new', 1)
                         ->where('product_flat.channel', $channel)
                         ->where('product_flat.locale', $locale)
                         ->orderBy('product_id', 'desc');
        })->paginate($count);

        return $results;
    }

    /**
     * Returns best product
     *
     * @return \Illuminate\Support\Collection
     */
    public function getBestProducts($count)
    {
        $results = app(ProductFlatRepository::class)->scopeQuery(function ($query) {
            $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

            $locale = request()->get('locale') ?: app()->getLocale();

            return $query->distinct()
                ->addSelect('product_flat.*')
                ->where('product_flat.status', 1)
                ->where('product_flat.visible_individually', 1)
                ->where('product_flat.channel', $channel)
                ->where('product_flat.locale', $locale)
                ->inRandomOrder();
        })->paginate($count);

        // Log::debug('best: '.$results);
        return $results;
        // $test = $this->orderItemRepository->getModel()
        //             ->select(DB::raw('SUM(qty_ordered) as total_qty_ordered'))
        //             ->addSelect('id', 'product_id', 'product_type', 'name')
        //             ->whereNull('parent_id')
        //             ->groupBy('product_id')
        //             ->orderBy('total_qty_ordered', 'DESC')
        //             ->limit(5)
        //             ->get();

        // $test = app(OrderRepository::class)->scopeQuery(function ($query) {
        //     $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

        //     $locale = request()->get('locale') ?: app()->getLocale();

        //     return $query->distinct()
        //         ->addSelect('product_flat.*')
        //         ->where('product_flat.status', 1)
        //         ->where('product_flat.visible_individually', 1)
        //         ->where('product_flat.channel', $channel)
        //         ->where('product_flat.locale', $locale)
        //         ->inRandomOrder();
        // })->paginate($count);
        // Log::debug('order: '.$test);

    }
        /**
     * Returns trending product
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTrendingProducts($count)
    {
        $results = app(ProductFlatRepository::class)->scopeQuery(function ($query) {
            $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

            $locale = request()->get('locale') ?: app()->getLocale();

            return $query->distinct()
                ->addSelect('product_flat.*')
                ->where('product_flat.status', 1)
                ->where('product_flat.visible_individually', 1)
                ->where('product_flat.channel', $channel)
                ->where('product_flat.locale', $locale)
                ->inRandomOrder();
        })->paginate($count);

        return $results;
    }
        /**
     * Returns clearance product
     *
     * @return \Illuminate\Support\Collection
     */
    public function getClearanceProducts($count)
    {
        $results = app(ProductFlatRepository::class)->scopeQuery(function ($query) {
            $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

            $locale = request()->get('locale') ?: app()->getLocale();

            return $query->distinct()
                ->addSelect('product_flat.*')
                ->where('product_flat.status', 1)
                ->where('product_flat.visible_individually', 1)
                ->where('product_flat.channel', $channel)
                ->where('product_flat.locale', $locale)
                ->inRandomOrder();
        })->paginate($count);

        return $results;
    }
    /**
     * Search Product by Attribute
     *
     * @param  array  $params
     * @return \Illuminate\Support\Collection
     */
    public function searchProductsFromCategory($params)
    {
        //term is the name of search input
        $term = $params['term'] ?? '';
        $categoryId = $params['category'] ?? '';

        $results = app(ProductFlatRepository::class)->scopeQuery(function($query) use($term, $categoryId, $params) {
            $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

            $locale = request()->get('locale') ?: app()->getLocale();

            $query = $query->distinct()
                           ->addSelect('product_flat.*')
                           ->leftJoin('products', 'product_flat.product_id', '=', 'products.id')
                           ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
                           ->where('product_flat.status', 1)
                           ->where('product_flat.visible_individually', 1)
                           ->where('product_flat.channel', $channel)
                           ->where('product_flat.locale', $locale)
                           ->whereNotNull('product_flat.url_key');
          
            //get all related products
            if ($term)
                $query->where('product_flat.name', 'like', '%' . urldecode($term) . '%')
                      ->orWhere('product_flat.short_description', 'like', '%' . urldecode($term) . '%')
                      ->orWhere('product_flat.meta_keywords', 'like', '%' . urldecode($term) . '%');

            if ($categoryId && $categoryId !== "") {
                $query = $query->where('product_categories.category_id', $categoryId);
            }

            if (isset($params['sort'])) {
                $attribute = $this->attributeRepository->findOneByField('code', $params['sort']);

                if ($params['sort'] == 'price') {
                    if ($attribute->code == 'price') {
                        $query->orderBy('min_price', $params['order']);
                    } else {
                        $query->orderBy($attribute->code, $params['order']);
                    }
                } else {
                    $query->orderBy($params['sort'] == 'created_at' ? 'product_flat.created_at' : $attribute->code, $params['order']);
                }
            }

            $query = $query->leftJoin('products as variants', 'products.id', '=', 'variants.parent_id');

            $query = $query->where(function($query1) use($query) {
                $aliases = [
                    'products' => 'filter_',
                    'variants' => 'variant_filter_',
                ];

                foreach($aliases as $table => $alias) {
                    $query1 = $query1->orWhere(function($query2) use ($query, $table, $alias) {

                        foreach ($this->attributeRepository->getProductDefaultAttributes(array_keys(request()->input())) as $code => $attribute) {
                            $aliasTemp = $alias . $attribute->code;

                            $query = $query->leftJoin('product_attribute_values as ' . $aliasTemp, $table . '.id', '=', $aliasTemp . '.product_id');

                            $column = ProductAttributeValue::$attributeTypeFields[$attribute->type];

                            $temp = explode(',', request()->get($attribute->code));

                            if ($attribute->type != 'price') {
                                $query2 = $query2->where($aliasTemp . '.attribute_id', $attribute->id);

                                $query2 = $query2->where(function($query3) use($aliasTemp, $column, $temp) {
                                    foreach($temp as $code => $filterValue) {
                                        if (! is_numeric($filterValue))
                                            continue;

                                        $columns = $aliasTemp . '.' . $column;
                                        $query3 = $query3->orwhereRaw("find_in_set($filterValue, $columns)");
                                    }
                                });
                            } else {
                                $query2->where('product_flat.min_price', '>=', core()->convertToBasePrice(current($temp)))
                                       ->where('product_flat.min_price', '<=', core()->convertToBasePrice(end($temp)));
                            }
                        }
                    });
                }
            });

            return $query->groupBy('product_flat.id');
        })->paginate(isset($params['limit']) ? $params['limit'] : 9);

        return $results;
    }
}