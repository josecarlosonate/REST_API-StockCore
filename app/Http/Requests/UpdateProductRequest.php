<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'price' => 'sometimes|required|numeric|min:0|max:100000000|decimal:0,2',
            'is_active' => 'sometimes|required|boolean',
            'categories' => 'sometimes|required|array|min:1',
            'categories.*' => 'integer|distinct|exists:categories,id',
        ];
    }

    public function after(): array
    {
        return [
            function ($validator) {

                $fields = [
                    'name',
                    'description',
                    'price',
                    'is_active',
                    'categories',
                ];

                $inputs = $this->all();

                $hasUpdatableField = collect($fields)->contains(fn($field) => array_key_exists($field, $inputs));

                if (!$hasUpdatableField) {
                    $validator->errors()->add(
                        'request',
                        'Debe proporcionar al menos un campo válido para actualizar.'
                    );
                }
            }
        ];
    }
}
