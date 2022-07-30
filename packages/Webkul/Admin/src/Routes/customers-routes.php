<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Customer\AddressController;
use Webkul\Admin\Http\Controllers\Customer\CustomerController;
use Webkul\Admin\Http\Controllers\Customer\CustomerGroupController;
use Webkul\Product\Http\Controllers\ReviewController;
use Webkul\Admin\Http\Controllers\Customer\CustomerNoteController;

/**
 * Customers routes.
 */
Route::group(['middleware' => ['web', 'admin'], 'prefix' => config('app.admin_url')], function () {
    /**
     * Customer management routes.
     */
    Route::get('customers', [CustomerController::class, 'index'])->defaults('_config', [
        'view' => 'admin::customers.index',
    ])->name('admin.customer.index');

    Route::get('customers/create', [CustomerController::class, 'create'])->defaults('_config', [
        'view' => 'admin::customers.create',
    ])->name('admin.customer.create');

    Route::post('customers/create', [CustomerController::class, 'store'])->defaults('_config', [
        'redirect' => 'admin.customer.index',
    ])->name('admin.customer.store');

    Route::get('customers/edit/{id}', [CustomerController::class, 'edit'])->defaults('_config', [
        'view' => 'admin::customers.edit',
    ])->name('admin.customer.edit');

    // This is deprecated
//    Route::get('customers/note/{id}', [CustomerController::class, 'createNote'])->defaults('_config', [
//        'view' => 'admin::customers.note',
//    ])->name('admin.customer.note.create');
//
//    Route::put('customers/note/{id}', [CustomerController::class, 'storeNote'])->defaults('_config', [
//        'redirect' => 'admin.customer.index',
//    ])->name('admin.customer.note.store');

    Route::put('customers/edit/{id}', [CustomerController::class, 'update'])->defaults('_config', [
        'redirect' => 'admin.customer.index',
    ])->name('admin.customer.update');

    Route::post('customers/delete/{id}', [CustomerController::class, 'destroy'])->name('admin.customer.delete');

    Route::post('customers/masssdelete', [CustomerController::class, 'massDestroy'])->name('admin.customer.mass-delete');

    Route::post('customers/masssupdate', [CustomerController::class, 'massUpdate'])->name('admin.customer.mass-update');

    Route::get('customers/{id}/invoices', [CustomerController::class, 'invoices'])->name('admin.customer.invoices.data');

    Route::get('customers/{id}/orders', [CustomerController::class, 'orders'])->defaults('_config', [
        'view' => 'admin::customers.orders.index',
    ])->name('admin.customer.orders.data');

    /**
     * Customer's addresses routes.
     */
    Route::get('customers/{id}/addresses', [AddressController::class, 'index'])->defaults('_config', [
        'view' => 'admin::customers.addresses.index',
    ])->name('admin.customer.addresses.index');

    Route::get('customers/{id}/addresses/create', [AddressController::class, 'create'])->defaults('_config', [
        'view' => 'admin::customers.addresses.create',
    ])->name('admin.customer.addresses.create');

    Route::post('customers/{id}/addresses/create', [AddressController::class, 'store'])->defaults('_config', [
        'redirect' => 'admin.customer.addresses.index',
    ])->name('admin.customer.addresses.store');

    Route::get('customers/addresses/edit/{id}', [AddressController::class, 'edit'])->defaults('_config', [
        'view' => 'admin::customers.addresses.edit',
    ])->name('admin.customer.addresses.edit');

    Route::put('customers/addresses/edit/{id}', [AddressController::class, 'update'])->defaults('_config', [
        'redirect' => 'admin.customer.addresses.index',
    ])->name('admin.customer.addresses.update');

    Route::post('customers/addresses/delete/{id}', [AddressController::class, 'destroy'])->defaults('_config', [
        'redirect' => 'admin.customer.addresses.index',
    ])->name('admin.customer.addresses.delete');

    Route::post('customers/{id}/addresses', [AddressController::class, 'massDestroy'])->defaults('_config', [
        'redirect' => 'admin.customer.addresses.index',
    ])->name('admin.customer.addresses.massdelete');

    /**
     * Customer's notes routes.
     */
    Route::get('customers/{id}/notes', [CustomerNoteController::class, 'index'])->defaults('_config', [
        'view' => 'admin::customers.notes.index',
    ])->name('admin.customer.notes.index');

    Route::get('customers/{id}/notes/create', [CustomerNoteController::class, 'create'])->defaults('_config', [
        'view' => 'admin::customers.notes.create',
    ])->name('admin.customer.notes.create');

    Route::post('customers/{id}/notes', [CustomerNoteController::class, 'store'])->defaults('_config', [
        'redirect' => 'admin.customer.notes.index',
    ])->name('admin.customer.notes.store');

    Route::get('customers/notes/{id}/edit', [CustomerNoteController::class, 'edit'])
        ->name('admin.customer.notes.edit');

    Route::put('customers/{customer}/notes/{note}', [CustomerNoteController::class, 'update'])
        ->name('admin.customer.notes.update');

    Route::delete('customers/notes/{id}', [CustomerNoteController::class, 'destroy'])->defaults('_config', [
        'redirect' => 'admin.customer.notes.index',
    ])->name('admin.customer.notes.delete');

//    Route::resource('customers.notes', CustomerNoteController::class)
////        ->parameter('customer', 'id')
//        ->parameters(['customer' => 'id'])
//        ->names('admin.customer.notes');

    /**
     * Customer's reviews routes.
     */
    Route::get('reviews', [ReviewController::class, 'index'])->defaults('_config', [
        'view' => 'admin::customers.reviews.index',
    ])->name('admin.customer.review.index');

    Route::get('reviews/edit/{id}', [ReviewController::class, 'edit'])->defaults('_config', [
        'view' => 'admin::customers.reviews.edit',
    ])->name('admin.customer.review.edit');

    Route::put('reviews/edit/{id}', [ReviewController::class, 'update'])->defaults('_config', [
        'redirect' => 'admin.customer.review.index',
    ])->name('admin.customer.review.update');

    Route::post('reviews/delete/{id}', [ReviewController::class, 'destroy'])->defaults('_config', [
        'redirect' => 'admin.customer.review.index',
    ])->name('admin.customer.review.delete');

    Route::post('reviews/massdestroy', [ReviewController::class, 'massDestroy'])->defaults('_config', [
        'redirect' => 'admin.customer.review.index',
    ])->name('admin.customer.review.massdelete');

    Route::post('reviews/massupdate', [ReviewController::class, 'massUpdate'])->defaults('_config', [
        'redirect' => 'admin.customer.review.index',
    ])->name('admin.customer.review.massupdate');

    /**
     * Customer groups routes.
     */
    Route::get('groups', [CustomerGroupController::class, 'index'])->defaults('_config', [
        'view' => 'admin::customers.groups.index',
    ])->name('admin.groups.index');

    Route::get('groups/create', [CustomerGroupController::class, 'create'])->defaults('_config', [
        'view' => 'admin::customers.groups.create',
    ])->name('admin.groups.create');

    Route::post('groups/create', [CustomerGroupController::class, 'store'])->defaults('_config', [
        'redirect' => 'admin.groups.index',
    ])->name('admin.groups.store');

    Route::get('groups/edit/{id}', [CustomerGroupController::class, 'edit'])->defaults('_config', [
        'view' => 'admin::customers.groups.edit',
    ])->name('admin.groups.edit');

    Route::put('groups/edit/{id}', [CustomerGroupController::class, 'update'])->defaults('_config', [
        'redirect' => 'admin.groups.index',
    ])->name('admin.groups.update');

    Route::post('groups/delete/{id}', [CustomerGroupController::class, 'destroy'])->name('admin.groups.delete');
});
