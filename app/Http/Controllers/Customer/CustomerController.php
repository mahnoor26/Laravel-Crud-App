<?php

namespace App\Http\Controllers\Customer;

use App\Traits\FileCascade;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Requests\Customer\UploadCustomerFileRequest;
use App\Services\CustomerService;
use F9Web\ApiResponseHelpers;

class CustomerController extends Controller
{
    use ApiResponseHelpers, FileCascade;

    public function __construct(private readonly CustomerService $customerService) {}

    /**
     * Get all customers
     */
    public function index()
    {
        $customers = $this->customerService->getAllCustomers();

        return $this->respondWithSuccess([
            'success' => true,
            'message' => 'Customers retrieved successfully',
            'customers' => $customers,
        ]);
      
    }

    /**
     * Get a specific customer
     */
    public function show($id)
    {
        $customer = $this->customerService->getCustomerById($id);

        return $this->respondWithSuccess([
            'success' => true,
            'message' => 'Customer retrieved successfully',
            'customer' => $customer,
        ]);
       
    }

    /**
     * Create a new customer
     */
    public function store(StoreCustomerRequest $request)
    {
        $customer = $this->customerService->createCustomer($request->validated());

        return $this->respondCreated([
            'success' => true,
            'message' => 'Customer created successfully',
            'customer' => $customer->load('creator'),
        ], 201);
     
    }

    /**
     * Update a customer
     */
    public function update($id, UpdateCustomerRequest $request)
    {
        
        $customer = $this->customerService->updateCustomer($id, $request->validated());

        return $this->respondWithSuccess([
            'success' => true,
            'message' => 'Customer updated successfully',
            'customer' => $customer,
        ]);
      
    }

    /**
     * Delete a customer
     */
    public function destroy($id)
    {
        $this->customerService->deleteCustomer($id);

        return $this->respondWithSuccess([
            'success' => true,
            'message' => 'Customer deleted successfully',
        ]);
     
    }

    /**
     * Upload file for a customer
     */
    public function uploadFile(UploadCustomerFileRequest $request)
    {
            $file = $this->customerService->uploadCustomerFile(
                $request->validated('customer_id'),
                $request->file('file')
            );

            return $this->respondWithSuccess([
                'success' => true,
                'message' => 'File uploaded successfully',
                'file' => $file->load('uploader'),
            ], 201);
      
    }

    /**
     * Get all files for a customer
     */
    public function getFiles($customerId)
    {
            $files = $this->customerService->getCustomerFiles($customerId);

            return $this->respondWithSuccess([
                'success' => true,
                'message' => 'Files retrieved successfully',
                'files' => $files,
            ]);
    
    }

    /**
     * Delete a file for a customer
     */
    public function deleteFile($customerId, $fileId)
    {
            $this->customerService->deleteCustomerFile($customerId, $fileId);

            return $this->respondWithSuccess([
                'success' => true,
                'message' => 'File deleted successfully',
            ]);
    
    }
}
