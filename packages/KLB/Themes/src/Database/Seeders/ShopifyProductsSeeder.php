<?php

namespace KLB\Themes\Database\Seeders;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PHPShopify\ShopifySDK;
use Webkul\Attribute\Models\AttributeFamily;
use Webkul\Attribute\Models\AttributeOption;
use Webkul\Attribute\Repositories\AttributeFamilyRepository;
use Webkul\Attribute\Repositories\AttributeOptionRepository;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\BulkUpload\Repositories\Products\ConfigurableProductRepository;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Inventory\Repositories\InventorySourceRepository;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductAttributeValue;
use Webkul\Product\Repositories\ProductAttributeValueRepository;
use Webkul\Product\Repositories\ProductFlatRepository;
use Webkul\Product\Repositories\ProductImageRepository;
use Webkul\Product\Repositories\ProductInventoryRepository;
use Webkul\Product\Repositories\ProductRepository;

class ShopifyProductsSeeder extends APISeeder
{
    /**
     * @var AttributeFamilyRepository
     */
    private $attributeFamilyRepository;

    /**
     * @var AttributeOptionRepository
     */
    private $attributeOptionRepository;

    /**
     * @var AttributeRepository
     */
    private $attributeRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var ConfigurableProductRepository
     */
    private $configurableProductRepository;

    /**
     * @var InventorySourceRepository
     */
    private $inventorySource;

    /**
     * @var ProductAttributeValueRepository
     */
    private $productAttributeValueRepository;

    /**
     * @var ProductFlatRepository
     */
    private $productFlatRepository;

    /**
     * @var ProductImageRepository
     */
    private $productImageRepository;

    /**
     * @var ProductInventoryRepository
     */
    private $productInventoryRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * Each time the create method is called, this property is updated with the
     * data for a given product from the Shopify API. This is the entire product
     * object from shopify, and lots of data needs to be manipulated on it for
     * this seeder to function.
     *
     * @var array
     */
    public $product;

    /**
     * This property will be updated multiple times for a given product being
     * seeded. This will start as the base product data for a given product -
     * a merger of the $this->product object and its first variant (the base
     * product). It will then be updated several times to seed each of the other
     * variants the $this->product model has.
     *
     * @var array
     */
    public $model;

    /**
     * This property will be filled with the base product data for each product.
     * This data includes sku, type, parent_id, barcode, attribute_family_id,
     * and yotpoid.
     *
     * @var array
     */
    public $baseProductData;

    /**
     * This property defines the validation scheme for creating a base product.
     * It will be filled according to what type of product we are creating.
     *
     * @var array
     */
    public $baseProductDataRules;

    /**
     * The base product model that is created by the createBaseProduct method.
     *
     * @var Product
     */
    private $baseProduct;

    /**
     * This property will be set to True for while seeding all product variants.
     * For the default product this will be false.
     *
     * @var bool
     */
    public $modelIsVariant;

    /**
     * This property will be used to keep track of the parent product SKU for
     * all variants of a given product. Initially, for the default product, this
     * will not be set.
     *
     * @var string
     */
    public $parentProductSku;

    /**
     * This property will be set with the data of the attribute family model
     * used for all products.
     *
     * @var AttributeFamily
     */
    public $attributeFamily;

    /**
     * This property will be set with the custom attributes for the attribute
     * family model used for all products.
     *
     * @var Collection
     */
    public $attributeFamilyOptions;

    /**
     * This property will hold the validation rules for the given attribute
     * family.
     *
     * @var array
     */
    public $attributeFamilyValidationRules;

    /**
     * this property will hold all attribute data for a given product
     *
     * @var array
     */
    public $modelAttributeData;

    /**
     * Setup the seeder with relevant data and repositories
     */
    public function __construct()
    {
        $this->attributeFamilyRepository = app(AttributeFamilyRepository::class);
        $this->attributeRepository = app(AttributeRepository::class);
        $this->attributeOptionRepository = app(AttributeOptionRepository::class);
        $this->categoryRepository = app(CategoryRepository::class);
        $this->configurableProductRepository = app(ConfigurableProductRepository::class);
        $this->inventorySource = app(InventorySourceRepository::class);
        $this->productAttributeValueRepository = app(ProductAttributeValueRepository::class);
        $this->productFlatRepository = app(ProductFlatRepository::class);
        $this->productImageRepository = app(ProductImageRepository::class);
        $this->productInventoryRepository = app(ProductInventoryRepository::class);
        $this->productRepository = app(ProductRepository::class);
    }

    /**
     * Make the requests to the Shopify API to fetch all product data. The
     * products endpoint is paginated, so we have to call it a bunch of times
     * to get all of the products.
     *
     * @throws \PHPShopify\Exception\ApiException
     * @throws \PHPShopify\Exception\CurlException
     * @throws \PHPShopify\Exception\SdkException
     * @throws Exception
     *
     * @return Collection
     */
    public function makeApiRequest(): Collection
    {
        if ($this->api instanceof ShopifySDK) {
            $params = ['published_status' => 'published', 'status' => 'active'];
            /**get and count all products that has published + active status
             * Divide the api calls to 250 per request
             */
            $count = $this->api->Product->count($params);
            $numberOfRequests = ceil($count / 250);
            Log::debug("Making $numberOfRequests API Calls to get $count products from shopify. This may take a while.");

            $lastId = 1;
            $currentCall = 0;
            $products = collect();

            do {
                /**
                 * call shopify api and get all products(250 at a time) until there are no products to be found
                 */
                $results = $this->api->Product->get(array_merge(['limit' => 250, 'since_id' => $lastId], $params));
                $products = $products->merge($results);
                $lastId = end($results)['id']??'';
                $currentCall += 1;
                $currentNumberOfProducts = $products->count();
                Log::debug("Finished API Call $currentCall of $numberOfRequests. ($currentNumberOfProducts / $count products retrieved).");
            } while (sizeof($results) > 0);

            return $products;
        }

        return null;
    }

    /**
     * Create the product models associated with the given shopify product.
     *
     * @param array $product
     *
     * @return bool
     */
    public function create($product)
    {
        $this->product = $product;
        $this->resetProperties();

        try {
            $this->createDefaultProduct();
            $this->handleProductVariants();

            return true;
        } catch (Exception $e) {
            Log::error($e);
            $this->deleteBaseProductIfExists();

            return false;
        }
    }

    /**
     * This method resets all pertinent properties to their default null values.
     *
     * @return void
     */
    public function resetProperties()
    {
        $this->model = null;
        $this->modelIsVariant = null;
        $this->parentProductSku = null;
        $this->baseProductData = null;
    }

    /**
     * First step in product creation -- the default product. This method is
     * responsible for creating the parent product of a series of product
     * variants (a configurable product), or a simple product if there is only
     * one variant.
     *
     * @throws Exception
     *
     * @return void
     */
    public function createDefaultProduct()
    {
        $this->setDefaultVariantInformation();
        $this->createProduct();
    }

    /**
     * After we do the default (parent) product, lets handle the product
     * variants!
     *
     * @throws Exception
     *
     * @return void
     */
    public function handleProductVariants()
    {
        if ($this->modelHasVariants()) {
            $this->modelIsVariant = true;
            $variants = $this->product['variants'];
            $this->parentProductSku = $this->model['sku'];
            foreach ($variants as $variantIndex => $variant) {
                // Don't try to seed the parent (default) product again
                if ($variantIndex > 0) {
                    $this->setVariantInformation($variantIndex);
                    $this->createProduct();
                }
            }
        }
    }

    /**
     * Setup the default product information for a product. This method will
     * get the first variant of the current product and add its data to the
     * model. It will also remove the variants property from the model, as the
     * parent product will not need to know about its children just yet.
     *
     * @return void
     */
    public function setDefaultVariantInformation()
    {
        $this->modelIsVariant = false;
        $this->setVariantInformation(0);
    }

    /**
     * This is a helper method used to reduce code duplication, basically is
     * responsible for setting the $this->model property for every product and
     * variant.
     *
     * @param int $variantIndex - The index of the variant in the Variants array
     *
     * @return void
     */
    public function setVariantInformation(int $variantIndex)
    {
        $variant = $this->product['variants'][$variantIndex];
        $this->model = array_merge($this->product, $variant);
        $this->model['name'] = $this->product['title'] . ' ' . $variant['title'];
        unset($this->model['variants']);
    }

    /**
     * This method will be called to handle the creation of every single product
     * model in the shopify data. It will do everything from creating the base
     * product to adding images, attributes, and the rest of the necessary
     * information for every product.
     *
     * @throws Exception
     *
     * @return bool
     */
    public function createProduct()
    {
        $this->createBaseProduct();
        $this->insertProductAttributes();
        $this->insertProductImages();
        $this->insertProductInventories();
        $this->insertProductCategories();
        $this->createProductFlat();

        return $this->baseProductExists();
    }

    /**
     * Handle creating the base product model. This has the very basic
     * information for a product. The method will ensure the data exists and is
     * of the proper form for the given product type.
     *
     * @throws Exception
     *
     * @return void
     */
    public function createBaseProduct()
    {
        $this->ensureProductDoesntAlreadyExist();
        $this->setBaseProductDataForModel();
        $this->setBaseProductValidationRules();
        $this->validateBaseProduct();

        $this->baseProduct = $this->productRepository->create($this->baseProductData);
    }

    /**
     * Make sure the product being seeded doesnt already exist. If it does,
     * throw an error so that the seeding does not continue for the product.
     *
     * @throws Exception if the product already exists
     *
     * @return void
     */
    public function ensureProductDoesntAlreadyExist()
    {
        $sku = $this->model['sku'];
        if ($this->findBaseProductBySku($sku)) {
            throw new Exception("Product already Exists: $sku");
        }
    }

    /**
     * This method will setup the data necessary for creating all base products
     * for every model and variant.
     *
     * @throws Exception if the Attribute family does not exist
     *
     * @return void
     */
    public function setBaseProductDataForModel()
    {
        $this->baseProductData = [
            'sku' => $this->model['sku'],
            'type' => $this->getProductType(),
            'parent_id' => $this->getProductParentId(),
            'attribute_family_id' => $this->getAttributeFamilyId(),
            'barcode' => $this->model['barcode'],
            'yotpoid' => $this->model['product_id'],
        ];
    }

    /**
     * Determine the type of product we are seeding here. If the product has
     * variants (the shopify variants property has more than one object) and the
     * model we are seeding is not a variant itself, it is a configurable
     * product. Otherwise, we are seeding a simple product.
     */
    public function getProductType(): string
    {
        return $this->modelHasVariants() ? 'configurable' : 'simple';
    }

    /**
     * Determine if a given model has variants. This is only really a "smart"
     * method for the initial product, when seeding variants it always returns
     * false. I really fucking hope there aren't SUB-Variants. That'd really
     * fuck with my day.
     *
     * @return bool
     */
    public function modelHasVariants(): bool
    {
        if ($this->modelIsVariant) {
            return false;
        }

        return $this->getProductVariants()->count() > 1;
    }

    /**
     * Get the variants from the shopify product model as a collection so they
     * are easier to work with.
     *
     * @reutrn Collection
     */
    public function getProductVariants(): Collection
    {
        return collect($this->product['variants']);
    }

    /**
     * Get the current variant product's parent id. If the parent doesn't exist
     * throw an error as we cannot continue.
     *
     * @throws Exception
     *
     * @return int | null
     */
    public function getProductParentId()
    {
        if ($this->modelIsVariant) {
            $parent = $this->findBaseProductBySku($this->parentProductSku);
            if ($parent) {
                return $parent->id;
            }

            throw new Exception('Parent Product does not exist.');
        }

        return null;
    }

    /**
     * Get a product by its sku.
     *
     * @param $sku - The SKU to look for
     *
     * @return Product
     */
    private function findBaseProductBySku($sku)
    {
        return $this->productRepository->findOneByField('sku', $sku);
    }

    /**
     * Get the attribute family to be used for importing all products. This
     * will always be the same, so we will save the id to a property and return
     * it after the initial lookup.
     *
     * @param string $name - The name of the attribute family to find
     *
     * @return int
     * @throws Exception
     */
    public function getAttributeFamilyId($name = "Shopify"): int
    {
        if ($this->attributeFamily == null) {
            $attributeFamily = $this->attributeFamilyRepository->findOneByfield('name', $name);
            if ($attributeFamily === null) {
                throw new Exception("Attribute family $name does not exist but is required.");
            }

            $this->attributeFamily = $attributeFamily;
            $this->attributeFamilyOptions = $attributeFamily->custom_attributes;
        }

        return $this->attributeFamily->id;
    }

    /**
     * Set the base product validation rules based on whether or not the product
     * being seeded is a variant or not.
     *
     * @return void
     */
    public function setBaseProductValidationRules()
    {
        $this->baseProductDataRules = [
            'sku' => 'required|string',
            'type' => 'required|string',
            'parent_id' => $this->modelIsVariant ? 'required' : 'nullable',
            'attribute_family_id' => 'required',
            'barcode' => 'nullable',
            'yotpoid' => 'nullable',
        ];
    }

    /**
     * Validate the Base Product data, utilizing the $this->baseProductRules
     * rules that are configured above, and modified depending on if the product
     * is a child or parent.
     */
    private function validateBaseProduct()
    {
        $baseProductValidator = Validator::make($this->baseProductData, $this->baseProductDataRules);
        if ($baseProductValidator->fails()) {
            Log::error('ShopifyProductsSeeder data did not meet minimum requirements:');
            throw new Exception($baseProductValidator->errors()->toJson());
        }
    }

    /**
     * This command will delete a product if it exists. This is used normally
     * only after a validation failure.
     */
    private function deleteBaseProductIfExists()
    {
        $baseProduct = $this->findBaseProductBySku($this->model['sku']);
        if ($baseProduct) {
            $baseProduct->delete();
        }
    }

    /**
     * This method will handle validating and inserting the product attributes.
     *
     * @throws Exception if the product does not meet requirements
     *
     * @return void
     */
    public function insertProductAttributes()
    {
        $this->setAttributeData();
        $this->generateProductAttributeFamilyValidationRules();
        $this->validateProductAttributeFamilyAttributes();

        // ensure all necessary data is properly formatted on the $data object,
        // pulled from the $this->model in the getAttributeValues method bellow
        $data = $this->getAttributeValues();
        $data['channel'] = core()->getCurrentChannel()->code;
        $data['locale'] = core()->getCurrentLocale()->code;
        $data['categories'] = $this->getCategories();

        // Go over each of the attributes from the product's attribute family
        // and insert each of their values from the data object
        foreach ($this->attributeFamilyOptions as $attribute) {
            $valueColumn = ProductAttributeValue::$attributeTypeFields[$attribute->type];
            $this->productAttributeValueRepository->create([
                'product_id' => $this->baseProduct->id,
                'attribute_id' => $attribute->id,
                $valueColumn => array_key_exists($attribute->code, $data) ? $data[$attribute->code] : '',
                'channel' => $attribute->value_per_channel ? $data['channel'] : null,
                'locale' => $attribute->value_per_locale ? $data['locale'] : null
            ]);
        }
    }

    /**
     * This method takes the shopify data and mutates it in a way that bagisto
     * expects. This means looking up things like the ID of the attribute values
     * themselves, and setting default values for properties that dont exist in
     * the shopify data. This method in turn sets the modelAttributeData
     * property with the aforementioned data.
     *
     * @throws Exception
     *
     * @return void
     */
    public function setAttributeData()
    {
        $new = $featured = 0;
        // Local Environment set some products as New and Featured
        if (env('APP_ENV') == 'local') {
            // a product has a 25% chance of being "new" on local
            $new = random_int(1, 100) > 75 ? 1 : 0;
            // a product has a 10% chance of being "featured" on local
            $featured = random_int(1, 100) > 90 ? 1 : 0;
        }

        $attributeMap = collect([
            "short_description" => 'body_html',
            "description" => 'body_html',
            "sku" => 'sku',
            "name" => 'name',
            "url_key" => [
                'computed' => 'handle',
            ],
            "new" => [
                'default' => $new,
            ],
            "featured" => [
                'default' => $featured,
            ],
            "visible_individually" => [
                'default' => !$this->modelIsVariant,
            ],
            "status" => [
                'default' => 1,
            ],
            "color" => [
                'computed' => 'color',
            ],
            "size" => [
                'computed' => 'size',
            ],
            "brand" => [
                'computed' => 'vendor',
            ],
            "guest_checkout" => [
                'default' => 1,
            ],
            "meta_title" => 'title',
            "meta_keywords" => 'tags',
            "meta_description" => 'body_html',
            "price" => 'compare_at_price',
            "cost" => [
                'computed' => 'cost'
            ],
            "special_price" => 'price',
            "weight" => [
                'computed' => 'weight'
            ],
        ]);

        $attributeMap->each(function ($shopifyKey, $bagistoKey) {
            if (!is_array($shopifyKey)) {
                $this->modelAttributeData[$bagistoKey] = $this->model[$shopifyKey];
            } else {
                $method = array_keys($shopifyKey)[0];
                $property = array_values($shopifyKey)[0];
                switch ($method) {
                    case 'default':
                        break;
                    case 'computed':
                        switch ($property) {
                            case 'handle':
                                $property = 'products/' . $this->model['handle'];
                                break;
                            case 'vendor':
                                $property = $this->getBagistoAttributeOptionValueByName('brand', $this->model['vendor'])->id;
                                break;
                            case 'cost':
                                $inventoryItem = $this->api->InventoryItem->get(['ids'=>$this->model['inventory_item_id']])[0];
                                $property = $inventoryItem['cost'];
                                break;
                            case 'weight':
                                $weight = floatval($this->model['weight']);
                                if ($weight == 0.0) {
                                    $property = 1.0;
                                } else {
                                    $property = $weight;
                                }
                                break;
                            default:
//                                $property = null; <--- Im a dumb-ass
                                if ($this->variantHasOption($property)) {
                                    $optionValue = $this->getVariantOptions()[$property];
                                    $property = $this->getBagistoAttributeOptionValueByName($property, $optionValue)->id;
                                } else {
                                    $property = null; // <--- that's where its supposed to go!
                                }
                                break;
                        }
                        break;
                }
                $this->modelAttributeData[$bagistoKey] = $property;
            }
        });
    }

    /**
     * Check whether the current product has a given option.
     *
     * @param $optionCode - The option code to look for in the product model.
     *
     * @return bool
     */
    public function variantHasOption($optionCode)
    {
        return collect($this->product['options'])->where('name', ucwords($optionCode))->count() > 0;
    }

    /**
     * Get all variant options that are present on the shopify product model.
     * These will be used to look up the attribute value ids later.
     *
     * @return Collection
     */
    public function getVariantOptions()
    {
        $optionNames = collect($this->product['options'])->pluck('name')->map(function ($option) {
            return strtolower($option);
        });

        $optionValues = collect();
        collect(['option1', 'option2', 'option3'])->each(function ($optionKey, $optionIndex) use ($optionNames, $optionValues) {
            if ($optionNames->count() > $optionIndex) {
                $optionValues->put($optionNames[$optionIndex], $this->model[$optionKey]);
            }
        });

        // ['color' => '1W1 Bone']

        return $optionValues->reject(function ($optionValue, $optionName) {
            return ($optionValue == null || $optionName == '');
        });
    }

    /**
     * Look up a attribute option value by its attribute code ('size') and its
     * value ('s').
     *
     * @TODO This method has bugs! Working on it...
     *
     * @param $attributeCode - the attribute to look up options for
     * @param $optionValue - the option to look in the given attribute
     *
     * @return AttributeOption
     */
    public function getBagistoAttributeOptionValueByName($attributeCode, $optionValue)
    {
        $attribute = $this->attributeRepository->findOneByField('code', $attributeCode);
        if ($attribute) {
            $option = $attribute->options()->where('swatch_value', $optionValue);

            return $option->first();
        }

        Log::error("Attribute Code $attributeCode does not exist! Cannot lookup $optionValue value.");
        return null;
    }

    /**
     * Apply the validation rules from above to the current product.
     *
     * @throws Exception if the validation fails
     *
     * @return void
     */
    private function validateProductAttributeFamilyAttributes()
    {
        $validator = Validator::make($this->modelAttributeData, $this->attributeFamilyValidationRules);
        if ($validator->fails()) {
            $this->deleteBaseProductIfExists();
            Log::error('ProductsSeeder data for ' . $this->model['sku'] . ' did not meet attribute requirements:');
            throw new Exception($validator->errors()->toJson());
        }
    }

    /**
     * This is complex logic that I "stole" mostly from the Bulk Upload package.
     * It basically just generates the necessary validation rules for the
     * current model's attribute family's attribute options, and sets the
     * attributeFamilyValidationRules property.
     *
     * @return void
     */
    private function generateProductAttributeFamilyValidationRules()
    {
        $rules = [
            'special_price_from' => 'nullable|date',
            'special_price_to'   => 'nullable|date|after_or_equal:special_price_from',
            'special_price'      => ['nullable', new \Webkul\Core\Contracts\Validations\Decimal, 'lt:price']
        ];

        if ($this->baseProduct) {
            $rules['sku'] = ['required', 'unique:products,sku,' . $this->baseProduct->id, new \Webkul\Core\Contracts\Validations\Slug];
        }

        $attributeFamily = $this->attributeFamily;

        if ($attributeFamily instanceof AttributeFamily) {
            foreach ($this->attributeFamilyOptions as $attribute) {
                $attributeRules = [ $attribute->is_required ? 'required' : 'nullable' ];

                if ($attribute->validation == 'decimal' || $attribute->type == 'price') {
                    array_push($attributeRules, new \Webkul\Core\Contracts\Validations\Decimal);
                }

                if ($attribute->is_unique) {
                    array_push($attributeRules, function ($field, $value, $fail) use ($attribute) {
                        $column = ProductAttributeValue::$attributeTypeFields[$attribute->type];
                        if ($this->baseProduct) {
                            $valueIsUnique = $this->productAttributeValueRepository->isValueUnique($this->baseProduct->id, $attribute->id, $column, request($attribute->code));
                            if (!$valueIsUnique) {
                                $fail('The :attribute has already been taken.');
                            }
                        }
                    });
                }

                $rules[$attribute->code] = $attributeRules;
            }
        }

        $this->attributeFamilyValidationRules = $rules;
    }

    /**
     * Parse the data from the current model and retrieve all of the attributes
     * and their values that are required by the attribute family and present
     * them in an easy way.
     *
     * @return array
     */
    public function getAttributeValues()
    {
        $attributes = $this->attributeFamilyOptions->pluck('code')->filter(function ($attribute) {
            return array_key_exists($attribute, $this->modelAttributeData);
        });

        $values = $attributes->map(function ($attribute) {
            return $this->modelAttributeData[$attribute];
        });

        return array_combine($attributes->toArray(), $values->toArray());
    }

    /**
     * Find all images associated with this current product and ensure they are
     * downloaded to the server. Attach them to the current product when done.
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return void
     */
    public function insertProductImages()
    {
        if ($this->modelIsVariant) {
            $variantId = $this->model['id'];
            $images = collect($this->product['images'])->filter(function ($imageData) use ($variantId) {
                return in_array($variantId, $imageData['variant_ids']);
            })->pluck('src');
        } else {
            $images = [$this->product['image']['src']];
        }

        foreach ($images as $image) {
            $localSrc = str_replace('https://cdn.shopify.com/s/files/1/0256/6140/7277', '', $image);
            $localSrc = strtok($localSrc, '?');

            if (!$this->imageExists($localSrc)) {
                $this->downloadShopifyImage($image, $localSrc);
            }

            $this->insertImage($localSrc);
        }
    }

    /**
     * Check if a given image src exists in the local public folder.
     *
     * @param $imageSrc - The path to look for
     *
     * @return bool
     */
    public function imageExists($imageSrc)
    {
        return Storage::disk('public')->exists($imageSrc);
    }

    /**
     * Given a file source and a local path, download and save the current
     * product images to the public folder of the site.
     *
     * @param $src - The external url of the image to download
     * @param $localPath - The local storage path for the image
     *
     * @return void
     */
    public function downloadShopifyImage($src, $localPath)
    {
        $contents = file_get_contents($src);

        Storage::disk('public')->put($localPath, $contents);
    }

    /**
     * Insert a given product image path to the current base product.
     *
     * @param string $image - The local public path of the image
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return void
     */
    private function insertImage($image)
    {
        $this->productImageRepository->create([
            'path'       => $image,
            'product_id' => $this->baseProduct->id,
        ]);
    }

    /**
     * Insert the default inventory source for the product with the given
     * quantity from the api
     *
     * @return void
     */
    private function insertProductInventories()
    {
        $inventories = [];
        $inventorySourceId = $this->inventorySource->findOneByfield('code', 'default')->pluck('id')->toArray()[0];
        $inventories[$inventorySourceId] = $this->model['inventory_quantity'];

        $this->productInventoryRepository->saveInventories(['inventories' => $inventories], $this->baseProduct);
    }

    /**
     * Go over
     */
    public function insertProductCategories()
    {
        $categories = $this->getCategories();
        $this->baseProduct->categories()->sync($categories);
    }

    /**
     * This is a kinda sus method. The assumption here is that the product type
     * is the category for the product. This is not a great assumption, and we
     * will probably have to make this call the API to get the true Collection
     * names that the product belongs to, but I was stuck on that for an hour so
     * I decided it wasn't worth it. And now we have this!
     *
     * @return Collection
     */
    private function getCategories():Collection
    {
        $productType = strtolower($this->model['product_type']);
        $productType = preg_replace('/[^A-Za-z0-9 ]+/', '', $productType);
        $productType = str_replace(' ', '-', $productType);

        return collect($this->getCategory($productType));
    }

    /**
     * This method looks up the bagisto category model for a given category slug
     * and that model's id is returned.
     *
     * @param string $categorySlug
     *
     * @return int
     */
    private function getCategory(string $categorySlug):int
    {
        return $this->categoryRepository->findBySlugOrFail($categorySlug)->id;
    }

    /**
     * Utilize the BulkUpload configurableProductRepository to create the
     * product flat.
     *
     * @return void
     */
    private function createProductFlat()
    {
        $this->configurableProductRepository->createFlat($this->baseProduct, $this->getParentProduct());
    }

    /**
     * Get the parent of the given product. This will be determined by the
     * parentProductSku property.
     *
     * @return Product | null
     */
    private function getParentProduct()
    {
        if ($this->parentProductSku) {
            return $this->findBaseProductBySku($this->parentProductSku);
        }

        return null;
    }

    /**
     * Checks whether ot not a product exists by sku, the SKU to look for is
     * either the passed $sku or the one in the current $this->model['sku'].
     *
     * @param string | false $sku - If given a string, the SKU to look for
     *
     * @return bool
     */
    private function baseProductExists($sku = false)
    {
        $sku = $sku ? $sku : $this->model['sku'];

        return $this->findBaseProductBySku($sku) !== null;
    }
}