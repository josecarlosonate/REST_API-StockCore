<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    protected static function booted(): void
    {

        static::creating(function (Product $product) {

            do {
                $sku = 'SKU-' . Str::upper(Str::random(6));
            } while (Product::where('sku', $sku)->exists());

            $product->sku = $sku;
            $product->slug = Str::slug($product->name . '-' . $product->sku);
        });

        static::updating(function (Product $product) {

            if ($product->isDirty('name')) {
                $product->slug = Str::slug($product->name . '-' . $product->sku);
            }
        });
    }
}
