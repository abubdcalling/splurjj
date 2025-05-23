<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'slug',
        'image',
        'details',
        'tags',
        'keyword',
        'meta_description',
        'meta_title',
        'publish',
    ];
}
