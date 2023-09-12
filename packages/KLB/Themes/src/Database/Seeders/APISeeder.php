<?php

namespace KLB\Themes\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use PHPShopify\ShopifySDK;

abstract class APISeeder extends Seeder implements SeederInterface
{
    public $api;
    public $data;

    abstract public function makeApiRequest(): Collection;

    public function seedFromDataCollection()
    {
        //take data from makeApiRequest function and create each
        $this->data->each(function ($model) {
            $this->create($model);
        });
    }

    public function setupApi()
    {
        $this->api = new ShopifySDK([
            'ShopUrl'   => env('SHOPIFY_SHOP_URL'),
            'ApiKey'    => env('SHOPIFY_API_KEY'),
            'Password'  => env('SHOPIFY_API_PASSWORD'),
        ]);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->setupApi();
        $this->data = $this->makeApiRequest();
        $this->seedFromDataCollection();
    }
}