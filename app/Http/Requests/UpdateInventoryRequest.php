<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('inventory.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'min_stock' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'max_stock' => ['sometimes', 'nullable', 'integer', 'min:0'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator) {

                if (! $this->has('min_stock') && ! $this->has('max_stock')) {
                    $validator->errors()->add(
                        'request',
                        'Debe proporcionar al menos un campo válido para actualizar.'
                    );

                    return;
                }

                $product = $this->route('product');
                $inventory = $product->inventory;

                $minStock = $this->has('min_stock') ? $this->input('min_stock') : $inventory->min_stock;
                $maxStock = $this->has('max_stock') ? $this->input('max_stock') : $inventory->max_stock;

                if (
                    $minStock !== null &&
                    $maxStock !== null &&
                    $maxStock < $minStock
                ) {
                    $validator->errors()->add(
                        'request',
                        'El rango de stock no es válido: el stock mínimo debe ser menor o igual al stock máximo.'
                    );
                }
            },
        ];
    }
}
