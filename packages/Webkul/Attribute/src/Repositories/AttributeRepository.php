<?php

namespace Webkul\Attribute\Repositories;

use Webkul\Attribute\Models\Attribute;
use Webkul\Core\Eloquent\Repository;
use Illuminate\Support\Facades\Event;
use Webkul\Attribute\Repositories\AttributeOptionRepository;
use Illuminate\Container\Container as App;
use Illuminate\Support\Str;
use Webkul\Product\Models\ProductAttributeValue;
use Webkul\Product\Models\ProductFlat;

class AttributeRepository extends Repository
{
    /**
     * AttributeOptionRepository object
     *
     * @var \Webkul\Attribute\Repositories\AttributeOptionRepository
     */
    protected $attributeOptionRepository;

    /**
     * Create a new repository instance.
     *
     * @param  \Webkul\Attribute\Repositories\AttributeOptionRepository  $attributeOptionRepository
     * @return void
     */
    public function __construct(
        AttributeOptionRepository $attributeOptionRepository,
        App $app
    )
    {
        $this->attributeOptionRepository = $attributeOptionRepository;

        parent::__construct($app);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Attribute\Contracts\Attribute';
    }

    /**
     * @param  array  $data
     * @return \Webkul\Attribute\Contracts\Attribute
     */
    public function create(array $data)
    {
        Event::dispatch('catalog.attribute.create.before');

        $data = $this->validateUserInput($data);

        $options = isset($data['options']) ? $data['options'] : [];

        unset($data['options']);

        $attribute = $this->model->create($data);

        if (in_array($attribute->type, ['select', 'multiselect', 'checkbox']) && count($options)) {
            foreach ($options as $optionInputs) {
                $this->attributeOptionRepository->create(array_merge([
                    'attribute_id' => $attribute->id,
                ], $optionInputs));
            }
        }

        Event::dispatch('catalog.attribute.create.after', $attribute);

        return $attribute;
    }

    /**
     * @param  array  $data
     * @param  int $id
     * @param  string  $attribute
     * @return \Webkul\Attribute\Contracts\Attribute
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $data = $this->validateUserInput($data);

        $attribute = $this->find($id);

        Event::dispatch('catalog.attribute.update.before', $id);

        $attribute->update($data);

        $previousOptionIds = $attribute->options()->pluck('id');

        if (in_array($attribute->type, ['select', 'multiselect', 'checkbox'])) {
            if (isset($data['options'])) {
                foreach ($data['options'] as $optionId => $optionInputs) {
                    if (Str::contains($optionId, 'option_')) {
                        $this->attributeOptionRepository->create(array_merge([
                            'attribute_id' => $attribute->id,
                        ], $optionInputs));
                    } else {
                        if (is_numeric($index = $previousOptionIds->search($optionId))) {
                            $previousOptionIds->forget($index);
                        }

                        $this->attributeOptionRepository->update($optionInputs, $optionId);
                    }
                }
            }
        }

        foreach ($previousOptionIds as $optionId) {
            $this->attributeOptionRepository->delete($optionId);
        }

        Event::dispatch('catalog.attribute.update.after', $attribute);

        return $attribute;
    }

    /**
     * @param  int  $id
     * @return void
     */
    public function delete($id)
    {
        Event::dispatch('catalog.attribute.delete.before', $id);

        parent::delete($id);

        Event::dispatch('catalog.attribute.delete.after', $id);
    }

    /**
     * @param  array  $data
     * @return array
     */
    public function validateUserInput($data)
    {
        if ($data['is_configurable']) {
            $data['value_per_channel'] = $data['value_per_locale'] = 0;
        }

        if (! in_array($data['type'], ['select', 'multiselect', 'price', 'checkbox'])) {
            $data['is_filterable'] = 0;
        }

        if (in_array($data['type'], ['select', 'multiselect', 'boolean'])) {
            unset($data['value_per_locale']);
        }

        return $data;
    }

    /**
     * @param array<ProductFlat> | false $products
     *
     * @return array
     */
    public function getFilterAttributes($products = false)
    {
        $baseQuery = $this->model->where('is_filterable', 1);

        if ($products) {
            // This is a custom performance modification made to this method,
            // which changes the filter attribute options that are loaded to
            // include only those which are used in the supplied products. This
            // reduces the number of queries made to the database (in theory) on
            // page load.

            // The products here will be paginated, we'll have to deal with that
            // somehow, but for now only improve performance on product listings
            // with only one page...
            if (!$products->hasMorePages()) {
                $products = $products->toArray()['data'];
                $attributeOptions = collect($products)->map(function ($productFlat) {
                    // First, start with a collection of product Flats, and map it
                    // to a collection  of attribute option ids that are used in
                    // those products.

                    $product = ProductFlat::find($productFlat['id'])->product;
                    $variants = $product->variants->pluck('id')->push($product->id);
                    $superAttributes = $product->super_attributes;

                    // Query for the attribute values of each product.
                    $productOptionValues = ProductAttributeValue::whereIn('attribute_id', $superAttributes->pluck('id'))->whereIn('product_id', $variants);

                    // Get the Attribute Option Ids from the attribute option
                    // values used in each of the filterable attributes
                    $attributeOptionIds = $superAttributes->map(function ($attribute) use ($productOptionValues) {
                        $field = ProductAttributeValue::$attributeTypeFields[$attribute->type];
                        $values = $productOptionValues->pluck($field)->unique();

                        return  $values;
                    })->flatten();

                    return $attributeOptionIds;
                })->reject(function ($attributeOptionId) {
                    // Some products will have empty sets returned from the
                    // above code, as they have no configuration options. Remove
                    // those empty results.

                    return is_null($attributeOptionId);
                })->flatten()->unique();

                // Price should always be filterable.
                $price = Attribute::where('code', 'price')->pluck('id');

                // FINALLY make the modified query with performance optimized.
                return $baseQuery->with(['options' => function ($query) use ($attributeOptions, $price) {
                    $query->whereIn('id', $attributeOptions)->orWhereIn('attribute_id', $price);
                }])->get();
            }
        }

        return $baseQuery->with('options')->get();
    }

    /**
     *
     * @param  array  $codes
     * @return array
     */
    public function getProductDefaultAttributes($codes = null)
    {
        $attributeColumns  = ['id', 'code', 'value_per_channel', 'value_per_locale', 'type', 'is_filterable'];

        if (! is_array($codes) && ! $codes)
            return $this->findWhereIn('code', [
                'name',
                'description',
                'short_description',
                'url_key',
                'price',
                'special_price',
                'special_price_from',
                'special_price_to',
                'status',
            ], $attributeColumns);

        if (in_array('*', $codes)) {
            return $this->all($attributeColumns);
        }

        return $this->findWhereIn('code', $codes, $attributeColumns);
    }

    /**
     * @param  string  $code
     * @return \Webkul\Attribute\Contracts\Attribute
     */
    public function getAttributeByCode($code)
    {
        static $attributes = [];

        if (array_key_exists($code, $attributes)) {
            return $attributes[$code];
        }

        return $attributes[$code] = $this->findOneByField('code', $code);
    }

    /**
     * @param  \Webkul\Attribute\Contracts\AttributeFamily  $attributeFamily
     * @return \Webkul\Attribute\Contracts\Attribute
     */
    public function getFamilyAttributes($attributeFamily)
    {
        static $attributes = [];

        if (array_key_exists($attributeFamily->id, $attributes)) {
            return $attributes[$attributeFamily->id];
        }

        return $attributes[$attributeFamily->id] = $attributeFamily->custom_attributes;
    }

    /**
     * @return array
     */
    public function getPartial()
    {
        $attributes = $this->model->all();

        $trimmed = [];

        foreach($attributes as $key => $attribute) {
            if ($attribute->code != 'tax_category_id'
                && (
                    $attribute->type == 'select'
                    || $attribute->type == 'multiselect'
                    || $attribute->code == 'sku'
            )) {
                if ($attribute->options()->exists()) {
                    array_push($trimmed, [
                        'id'          => $attribute->id,
                        'name'        => $attribute->admin_name,
                        'type'        => $attribute->type,
                        'code'        => $attribute->code,
                        'has_options' => true,
                        'options'     => $attribute->options,
                    ]);
                } else {
                    array_push($trimmed, [
                        'id'          => $attribute->id,
                        'name'        => $attribute->admin_name,
                        'type'        => $attribute->type,
                        'code'        => $attribute->code,
                        'has_options' => false,
                        'options'     => null,
                    ]);
                }

            }
        }

        return $trimmed;
    }
}