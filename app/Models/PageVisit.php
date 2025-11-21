<?php

// app/Models/PageVisit.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageVisit extends Model
{
    protected $fillable = ['path','ip','user_agent'];
}
