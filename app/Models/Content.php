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
        'credits',
        'date',
        'sub_heading',
        'body',
        'image_1',
        'advertising',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];
}
