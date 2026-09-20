<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Enums\StockMovementType;
use Illuminate\Validation\Rule;

class StoreStockMovementRequest extends FormRequest
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
            'type' => ['required', Rule::enum(StockMovementType::class)],
            'quantity' => [
                'required',
                'integer',
                Rule::when($this->input('type') !== StockMovementType::ADJUSTMENT->value, 'min:1', 'min:0')
            ],
            'reason' => [
                Rule::when($this->input('type') === StockMovementType::ADJUSTMENT->value, 'required', 'nullable'),
                'string',
                'max:1000'
            ]
        ];
    }
}
