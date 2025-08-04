<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;
    protected $guarded = ["id"];

    /**
     * Get the category this subcategory belongs to
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get services for this subcategory
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'subcategory_id');
    }
}