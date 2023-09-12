<?php

namespace Webkul\Shop\Http\Controllers;

use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Core\Repositories\SliderRepository;
use Webkul\Product\Repositories\SearchRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;



class BrandController extends Controller
{
    /**
     * SliderRepository object
     *
     * @var \Webkul\Core\Repositories\SliderRepository
     */
    protected $sliderRepository;

    /**
     * SearchRepository object
     *
     * @var \Webkul\Core\Repositories\SearchRepository
     */
    protected $searchRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Core\Repositories\SliderRepository  $sliderRepository
     * @param  \Webkul\Product\Repositories\SearchRepository  $searchRepository
     * @return void
     */
    public function __construct(
        SliderRepository $sliderRepository,
        SearchRepository $searchRepository
    )
    {
        $this->sliderRepository = $sliderRepository;

        $this->searchRepository = $searchRepository;

        parent::__construct();
    }

    /**
     * function for getting brand
     *
     * @return \Illuminate\View\View
     */
    public function getBrand($parentbrand, $brandname)
    {
        $results = DB::select('select id from kalistabeauty.attribute_option_translations where label = ?', [$brandname]);
        $brandId = $results[0]->id;

        $results = DB::select('SELECT product_id FROM kalistabeauty.product_attribute_values WHERE integer_value = ? AND attribute_id = 25;', [$brandId]);
        $p = array();
        foreach($results as $product){

            array_push($p,$product->product_id);


        }
        //$p = implode(",",$p);
        Log::debug($p);
        //$p = trim($p,'\'"');
      $products = DB::select('SELECT * FROM kalistabeauty.product_flat WHERE id IN (' . implode(',', array_map('intval', $p)) . ')');
        //$results = DB::select('SELECT * FROM kalistabeauty.product_flat WHERE id IN (2573,2574)');

 //return $results;

       // return view($this->_config['view'],  compact('products'));


   Log::debug($products);

   Log::debug($parentbrand." ".$brandname);


    }


}
