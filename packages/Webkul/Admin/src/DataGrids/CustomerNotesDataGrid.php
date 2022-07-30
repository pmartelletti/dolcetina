<?php

namespace Webkul\Admin\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;

class CustomerNotesDataGrid extends DataGrid
{
    /**
     * Index column.
     *
     * @var int
     */
    protected $index = 'id';

    /**
     * Default sort order of datagrid.
     *
     * @var string
     */
    protected $sortOrder = 'desc';

    /**
     * Prepare query.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('customer_notes')
            ->leftJoin('admins', 'customer_notes.created_by', '=', 'admins.id')
            ->select('customer_notes.id', 'customer_notes.note', 'customer_notes.created_at', 'admins.name as created_by')
            ->where('customer_notes.customer_id', request('id'));

        $this->addFilter('created_by', 'admins.name');

        $this->setQueryBuilder($queryBuilder);
    }

    /**
     * Add columns.
     *
     * @return void
     */
    public function addColumns()
    {

        $this->addColumn([
            'index'      => 'created_by',
             'label'      => trans('admin::app.datagrid.author'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'note',
            'label'      => trans('admin::app.datagrid.note'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('admin::app.datagrid.created-date'),
            'type'       => 'date',
            'searchable' => false,
            'sortable'   => false,
            'filterable' => false,
        ]);

    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
//        $this->addAction([
//            'title'  => trans('admin::app.datagrid.view'),
//            'method' => 'GET',
//            'route'  => 'admin.sales.invoices.view',
//            'icon'   => 'icon eye-icon',
//        ]);

//        dd(trans('ui::app.datagrid.massaction.delete', ['resource' => 'note']));

        $this->addAction([
            'title'        => trans('admin::app.datagrid.edit'),
            'method'       => 'GET',
            'route'        => 'admin.customer.notes.edit',
            'icon'         => 'icon pencil-lg-icon',
        ]);

        $this->addAction([
            'title'        => trans('admin::app.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'admin.customer.notes.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => 'note']),
            'icon'         => 'icon trash-icon',
        ]);
    }
}
