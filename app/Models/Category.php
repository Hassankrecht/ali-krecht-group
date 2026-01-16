<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'parent_id', 'order'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function translations()
    {
        return $this->hasMany(\App\Models\ProductCategoryTranslation::class, 'category_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function getNameLocalizedAttribute()
    {
        $locale = app()->getLocale();
        $fallback = config('app.locale', 'en');

        $translation = $this->translations->firstWhere('locale', $locale)
            ?: $this->translations->firstWhere('locale', $fallback);

        return $translation->name ?? $this->name;
    }
}
