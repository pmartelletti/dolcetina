@extends('admin::layouts.content')

@section('page_title')
    {{ __('admin::app.customers.notes.title', ['customer_name' => $customer->first_name . ' ' . $customer->last_name]) }}
@stop

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>
                    <i class="icon angle-left-icon back-link" onclick="window.location = '{{ route('admin.customer.index') }}'"></i>

                    {{ __('admin::app.customers.notes.title', ['customer_name' => $customer->first_name . ' ' . $customer->last_name]) }}
                </h1>
            </div>

            <div class="page-action">
                <a href="{{ route('admin.customer.notes.create', ['id' => $customer->id]) }}" class="btn btn-lg btn-primary">
                    {{ __('admin::app.customers.notes.create-btn-title') }}
                </a>
            </div>
        </div>

        {!! view_render_event('bagisto.admin.customer.addresses.list.before') !!}

        <div class="page-content">
            <datagrid-plus src="{{ route('admin.customer.notes.index', $customer->id) }}"></datagrid-plus>
        </div>

        {!! view_render_event('bagisto.admin.customer.addresses.list.after') !!}
    </div>
@stop
