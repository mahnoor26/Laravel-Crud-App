<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\File;

class CustomerService
{
    public function __construct(private readonly FileService $fileService) {}

    /**
     * Get all customers
     */
    public function getAllCustomers()
    {
        return Customer::with('creator')->get();
    }

    /**
     * Get customer by ID
     */
    public function getCustomerById($id)
    {
        return Customer::with(['creator', 'files'])->findOrFail($id);
    }

    /**
     * Create a new customer
     */
    public function createCustomer(array $data)
    {
        $customer = Customer::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'status' => $data['status'] ?? 'active',
            'created_by' => auth()->id(),
        ]);

        return $customer->load('creator');
    }

    /**
     * Update customer
     */
    public function updateCustomer($id, array $data)
    {
        $customer = Customer::findOrFail($id);

        $customer->update([
            'name' => $data['name'] ?? $customer->name,
            'email' => $data['email'] ?? $customer->email,
            'phone' => $data['phone'] ?? $customer->phone,
            'status' => $data['status'] ?? $customer->status,
        ]);

        return $customer->load('creator');
    }

    /**
     * Delete customer and associated S3 files
     */
    public function deleteCustomer($id)
    {
        $customer = Customer::findOrFail($id);

        $this->fileService->deleteFilesForEntity('customer', $customer->id);

        $customer->delete();

        return true;
    }

    /**
     * Upload file for customer (delegates to unified S3 upload)
     */
    public function uploadCustomerFile($customerId, $file): File
    {
        return $this->fileService->uploadFile('customer', (int) $customerId, $file);
    }

    /**
     * Get customer files
     */
    public function getCustomerFiles($customerId)
    {
        $customer = Customer::findOrFail($customerId);

        return $customer->files()->with('uploader')->get();
    }

    /**
     * Delete customer file from S3 and database
     */
    public function deleteCustomerFile($customerId, $fileId)
    {
        $customer = Customer::findOrFail($customerId);
        $file = $customer->files()->findOrFail($fileId);

        $this->fileService->deleteFile($file->id);

        return true;
    }
}
