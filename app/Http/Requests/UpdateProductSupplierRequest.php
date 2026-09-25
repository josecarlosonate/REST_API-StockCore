<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductSupplierRequest extends FormRequest
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
            'supplier_sku' => ['sometimes', 'nullable', 'string', 'max:255'],
            'cost' => ['sometimes', 'required', 'numeric', 'min:0', 'max:100000000'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator) {
                $fields = [
                    'supplier_sku',
                    'cost',
                ];

                $input = $this->all();

                $hasUpdatableField = collect($fields)->contains(
                    fn ($field) => array_key_exists($field, $input)
                );

                if (! $hasUpdatableField) {
                    $validator->errors()->add(
                        'request',
                        'Debe proporcionar al menos un campo válido para actualizar.'
                    );
                }
            },
        ];
    }
}
