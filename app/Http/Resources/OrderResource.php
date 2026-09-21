<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'total' => $this->total,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'customer' => $this->whenLoaded(
                'customer',
                fn () => new CustomerResource($this->customer)
            ),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
