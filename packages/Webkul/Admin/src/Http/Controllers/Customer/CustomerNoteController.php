<?php

namespace Webkul\Admin\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Webkul\Admin\DataGrids\CustomerDataGrid;
use Webkul\Admin\DataGrids\CustomerNotesDataGrid;
use Webkul\Customer\Models\Customer;
use Webkul\Customer\Models\CustomerNote;
use Webkul\Customer\Models\CustomerNoteProxy;
use Webkul\Customer\Rules\VatIdRule;
use Webkul\Admin\DataGrids\AddressDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Customer\Repositories\CustomerAddressRepository;
use Webkul\User\Repositories\AdminRepository;

class CustomerNoteController extends Controller
{
    /**
     * Contains route related configuration.
     *
     * @var array
     */
    protected $_config;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Customer\Repositories\CustomerRepository  $customerRepository
     * @param  \Webkul\Customer\Repositories\CustomerAddressRepository  $customerAddressRepository
     * @return void
     */
    public function __construct(
        protected CustomerRepository $customerRepository,
        protected CustomerAddressRepository $customerAddressRepository
    )
    {
        $this->_config = request('_config');
    }

    /**
     * Fetch address by customer id.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function index($id)
    {
        if (request()->ajax()) {
            return app(CustomerNotesDataGrid::class)->toJson();
        }

        $customer = $this->customerRepository->find($id);

        return view($this->_config['view'], compact('customer'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function create($id)
    {
        $customer = $this->customerRepository->find($id);

        return view($this->_config['view'], compact('customer'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $data = $this->validate($request, [
            'notes' => 'required|string',
        ]);

        CustomerNote::create([
            'customer_id' => $id,
            'note' => $data['notes'],
            'created_by' => auth()->guard('admin')->id(),
        ]);

        session()->flash('success', 'Note created successfully.');

        return redirect()->route($this->_config['redirect'],  ['id' => $id]);
    }

    /**
     * @param Customer $customer
     * @param CustomerNote $note
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $note = CustomerNote::findOrfail($id);
        $customer = $note->customer;
        if ($note->created_by != auth()->guard('admin')->id()) {
            session()->flash('error', 'Solo el creador de esta nota puede editarla.');
            return redirect()->back();
        }

        return view('admin::customers.notes.edit', compact('note', 'customer'));
    }

    /**
     * Edit's the pre made resource of customer called address.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($customer, CustomerNote $note)
    {
        $data = $this->validate(request(), [
            'notes' => 'required|string',
        ]);

        $note->update(['note' => $data['notes']]);

        session()->flash('success', trans('admin::app.customers.notes.success-update'));

        return redirect()->route('admin.customer.notes.index', ['id' => $customer->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        CustomerNote::destroy($id);

        return response()->json([
            'redirect' => false,
            'message' => trans('admin::app.customers.notes.success-delete')
        ]);
    }

    /**
     * Mass delete the customer's addresses.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function massDestroy($id)
    {
        $addressIds = explode(',', request()->input('indexes'));

        foreach ($addressIds as $addressId) {
            $this->customerAddressRepository->delete($addressId);
        }

        session()->flash('success', trans('admin::app.customers.addresses.success-mass-delete'));

        return redirect()->route($this->_config['redirect'], ['id' => $id]);
    }
}
