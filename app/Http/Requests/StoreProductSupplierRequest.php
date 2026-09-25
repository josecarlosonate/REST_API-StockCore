<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductSupplierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('product-suppliers.manage');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'supplier_id' => [
                'required',
                'integer',
                'exists:suppliers,id',
                Rule::unique('product_supplier', 'supplier_id')
                    ->where(function ($query) {
                        return $query->where('product_id', $this->route('product')->id);
                    }),
            ],
            'supplier_sku' => ['nullable', 'string', 'max:255'],
            'cost' => ['required', 'numeric', 'min:0', 'max:100000000'],
        ];
    }
}
