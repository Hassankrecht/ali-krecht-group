<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price',
        'category_id',
        'stock',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function translations()
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function getTitleLocalizedAttribute()
    {
        return $this->getTranslationValue('title');
    }

    public function getDescriptionLocalizedAttribute()
    {
        return $this->getTranslationValue('description');
    }

    protected function getTranslationValue(string $field)
    {
        $locale = app()->getLocale();
        $fallback = config('app.locale', 'en');

        $translation = $this->translations->firstWhere('locale', $locale)
            ?: $this->translations->firstWhere('locale', $fallback);

        return $translation[$field] ?? $this->{$field};
    }
}
