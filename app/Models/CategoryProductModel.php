<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryProductModel extends Model
{
    public $timestamps = false; //set time to false
    protected $fillable = [
        'meta_keywords', 'category_name', 'thumbnail', 'slug_category_product', 'category_desc', 'category_status', 'category_order'
    ];
    protected $primaryKey = 'category_id';
    protected $table = 'tbl_category_product';

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
