<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'name_translations',
        'width',
        'length',
        'height',
        'material',
    ];

    protected $casts = [
        'name_translations' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getNameTranslatedAttribute()
    {
        $locale = app()->getLocale();
        if (isset($this->name_translations[$locale]) && $this->name_translations[$locale]) {
            return $this->name_translations[$locale];
        }
        // fallback to default name
        return $this->name;
    }
}
