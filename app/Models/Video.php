<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'heading',
        'credits',
        'date',
        'sub_heading',
        'body',
        'image_1',
        'advertising',
        'tags',
        'category_id',
        'subcategory_id',
    ];
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }
}
