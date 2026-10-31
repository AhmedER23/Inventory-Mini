<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'type', 'qty', 'note', 'before_qty', 'after_qty',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'before_qty' => 'integer',
            'after_qty' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getLocalizedNoteAttribute(): string
    {
        if (! $this->note) {
            return '-';
        }

        $transKey = 'ui.' . $this->note;
        $translated = __($transKey);

        return $translated !== $transKey ? $translated : $this->note;
    }
}
