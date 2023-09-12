<?php

namespace KLB\Themes\Datagrids;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;

class AbandonedCartDetailsDatagrid extends DataGrid
{
    protected $index = 'id';

    protected $sortOrder = 'desc';

    public function prepareQueryBuilder()
    {
        //can do a inner join with products table to have product view page 

        // $queryBuilder = DB::table('cart_items')
        // ->select('cart_id', 'sku', 'name', 'quantity', 'total');

        // $this->setQueryBuilder($queryBuilder);
    }
    //this is specified function with id as param
    public function prepareQueryBuilderWithParam($id)
    {
        //can do a inner join with products table to have product view page 

        $queryBuilder = DB::table('cart_items')
        ->join('product_images', 'cart_items.product_id', '=', 'product_images.product_id')
        ->select('product_images.path','cart_items.product_id','cart_items.sku', 'cart_items.name', 'cart_items.quantity', 'cart_items.total', 'cart_items.cart_id as id')
        ->where('cart_items.cart_id', $id)
        ->orderBy('id','desc');        

        // $queryBuilder = DB::table('cart_items')
        // ->select('cart_items.product_id','cart_items.sku', 'cart_items.name', 'cart_items.quantity', 'cart_items.total')
        // ->where('cart_items.cart_id', $id);

        $this->setQueryBuilder($queryBuilder);
    }
    // <a href="/admin/catalog/products/edit/1997" class="link">1234567890</a>

    public function addColumns()
    {
        $this->addColumn([
            'index'      => 'path',
            'label'      => 'Product Image',
            'type'       => 'imagePath',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);
        $this->addColumn([
            'index'      => 'sku',
            'label'      => 'Product SKU',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);
        $this->addColumn([
            'index'      => 'name',
            'label'      => 'Product Name',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);
        $this->addColumn([
            'index'      => 'quantity',
            'label'      => 'Quantity',
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);
        $this->addColumn([
            'index'      => 'total',
            'label'      => 'Total Price',
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);
        $this->addColumn([
            'index'      => 'product_id',
            'label'      => 'Product URL',
            'type'       => 'url',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);
    }

    public function prepareActions()
    {
        // $this->addAction([
        //     'title'  => trans('admin::app.datagrid.view'),
        //     'method' => 'GET',
        //     'route'  => 'themes.admin.customer.abandoned-cart-details',
        //     'icon'   => 'icon eye-icon',
        // ]);
    }
}