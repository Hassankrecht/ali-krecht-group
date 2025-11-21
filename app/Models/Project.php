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

}
