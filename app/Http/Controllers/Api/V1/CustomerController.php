<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::paginate(15);

        return CustomerResource::collection($customers);
    }

    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();

        $customer = Customer::create($data);
        $customer->refresh();

        return (new CustomerResource($customer))->response()->setStatusCode(201);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $data = $request->validated();
        $customer->update($data);
        $customer->refresh();

        return new CustomerResource($customer);
    }
}
