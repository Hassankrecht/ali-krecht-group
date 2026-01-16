<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
    'title',
    'description',
    'location',
    'main_image',
    'date',
    'status',
];

public function images()
{
    return $this->hasMany(ProjectImage::class);
}

public function categories()
{
    return $this->belongsToMany(ProjectCategory::class, 'project_project_category');
}

public function translations()
{
    return $this->hasMany(ProjectTranslation::class);
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
