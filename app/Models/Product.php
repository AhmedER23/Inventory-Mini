<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku', 'name', 'description', 'quantity', 'unit', 'low_stock_threshold',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'low_stock_threshold' => 'integer',
        ];
    }

    public function stockTransactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function transactions(): HasMany
    {
        return $this->stockTransactions();
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->quantity <= $this->low_stock_threshold;
    }

    public function getLocalizedUnitAttribute(): string
    {
        $unit = $this->attributes['unit'] ?? 'Adet';
        return __('ui.' . $unit);
    }

    public function getNameAttribute($value): string
    {
        if (preg_match('/^(?:Ürün|Product|منتج)\s+(\d+)$/ui', (string) $value, $matches)) {
            $num = $matches[1];
            $locale = app()->getLocale();
            if ($locale === 'ar') {
                return "منتج {$num}";
            } elseif ($locale === 'tr') {
                return "Ürün {$num}";
            } else {
                return "Product {$num}";
            }
        }

        return (string) $value;
    }

    public function getDescriptionAttribute($value): ?string
    {
        if (preg_match('/^(?:Ürün|Product|منتج)\s+(\d+)\s+için örnek ürün açıklaması\.$/ui', (string) $value, $matches)) {
            $num = $matches[1];
            $locale = app()->getLocale();
            if ($locale === 'ar') {
                return "وصف تجريبي للمنتج {$num}.";
            } elseif ($locale === 'tr') {
                return "Ürün {$num} için örnek ürün açıklaması.";
            } else {
                return "Sample product description for Product {$num}.";
            }
        }

        return $value;
    }
}
