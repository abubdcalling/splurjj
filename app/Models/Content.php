<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_id',
        'subcategory_id',
        'heading',
        'author',
        'date',
        'sub_heading',
        'body1',
        'image1',
        'advertising_image',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];
}
