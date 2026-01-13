<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'order',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_project_category');
    }

    public function translations()
    {
        return $this->hasMany(ProjectCategoryTranslation::class);
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
