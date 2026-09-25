<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('categories.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($this->route('category')),
            ],
            'description' => 'sometimes|nullable|string',
            'is_active' => 'sometimes|required|boolean',
        ];
    }

    public function after(): array
    {
        return [
            function ($validator) {
                $fields = [
                    'name',
                    'description',
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
                }
            },
        ];
    }
}
