<?php

namespace KLB\Themes\Datagrids;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;

class AbandonedCartDatagrid extends DataGrid
{
    protected $index = 'id';

    protected $sortOrder = 'desc';

    public function prepareQueryBuilder()
    {
        //getting active customer cart table 
        $queryBuilder = DB::table('cart')
            ->select('id', 'customer_first_name', 'customer_last_name', 'customer_email', 'grand_total','updated_at')
            ->whereNotNull(['customer_first_name', 'customer_last_name', 'customer_email', 'grand_total','updated_at'])
            ->where('is_active', 1);

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => 'Id',
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);
        $this->addColumn([
            'index'      => 'customer_first_name',
            'label'      => 'First Name',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);
        $this->addColumn([
            'index'      => 'customer_last_name',
            'label'      => 'Last Name',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);
        $this->addColumn([
            'index'      => 'customer_email',
            'label'      => 'Email',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);
        $this->addColumn([
            'index'      => 'grand_total',
            'label'      => 'Total',
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);
        $this->addColumn([
            'index'      => 'updated_at',
            'label'      => 'Last Edited',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'title'  => trans('admin::app.datagrid.view'),
            'method' => 'GET',
            'route'  => 'themes.admin.customer.abandoned-cart-details',
            'icon'   => 'icon eye-icon',
        ]);

        $this->addAction([
            'title'  => trans('admin::app.datagrid.email'),
            'method' => 'GET',
            'route'  => 'themes.admin.customer.abandoned-cart-email',
            'icon'   => 'icon pencil-lg-icon',
        ]);
    }

        /**
     * send mass emails to customers
     */
    public function prepareMassActions()
    {
        $this->addMassAction([
            'type'    => 'delete',
            'label'   => 'Send Email',
            'action'  => route('themes.admin.customer.abandoned-cart-mass-email'),
            'method'  => 'PUT',  

        ]);
    }
}