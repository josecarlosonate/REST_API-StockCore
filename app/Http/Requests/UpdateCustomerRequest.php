<?php

namespace App\Http\Requests;

use App\Enums\CustomerDocumentType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'document_type' => ['sometimes', 'nullable', Rule::enum(CustomerDocumentType::class)],
            'document_number' => ['sometimes', 'nullable', 'string', 'max:50', Rule::unique('customers', 'document_number')->ignore($this->route('customer'))],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'required', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator) {
                $fields = [
                    'name',
                    'document_type',
                    'document_number',
                    'email',
                    'phone',
                    'address',
                    'is_active',
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

                    return;
                }

                $customer = $this->route('customer');
                $documentType = array_key_exists('document_type', $input) ? $input['document_type'] : $customer->document_type;
                $documentNumber = array_key_exists('document_number', $input) ? $input['document_number'] : $customer->document_number;

                if (($documentType === null && $documentNumber !== null) ||
                    ($documentType !== null && $documentNumber === null)
                ) {
                    $validator->errors()->add(
                        'document',
                        'El tipo y el número de documento deben proporcionarse juntos.'
                    );
                }
            },
        ];
    }
}
