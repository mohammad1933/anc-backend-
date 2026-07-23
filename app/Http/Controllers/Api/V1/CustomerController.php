<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $customers = Customer::query()->withCount(['sampleRequests', 'inquiries'])
            ->when($request->string('industry')->isNotEmpty(), fn ($query) => $query->where('industry', $request->string('industry')))
            ->when($request->string('status')->isNotEmpty(), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->string('search')->isNotEmpty(), fn ($query) => $query->where(function ($query) use ($request) {
                $query->where('company_name', 'like', '%'.$request->string('search').'%')->orWhere('contact_name', 'like', '%'.$request->string('search').'%')->orWhere('email', 'like', '%'.$request->string('search').'%');
            }))->latest()->paginate($request->integer('per_page', 10));
        return CustomerResource::collection($customers);
    }
    public function store(UpsertCustomerRequest $request): CustomerResource { return new CustomerResource(Customer::create($request->validated())); }
    public function show(Customer $customer): CustomerResource { return new CustomerResource($customer->load(['sampleRequests.items', 'inquiries'])->loadCount(['sampleRequests', 'inquiries'])); }
    public function update(UpsertCustomerRequest $request, Customer $customer): CustomerResource { $customer->update($request->validated()); return new CustomerResource($customer->refresh()); }
    public function destroy(Customer $customer): JsonResponse { $customer->delete(); return response()->json(status: 204); }
}
