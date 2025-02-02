<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    // use HasFactory;
    public $timestamps = false; // set time to false
    protected $fillable = [
        'comment', 'comment_name', 'comment_date', 'comment_product_id', 'comment_status', 'comment_parent_comment', 'comment_email'
    ];
    protected $primaryKey = "comment_id";
    protected $table = 'tbl_comment';

    public function product()
    {
        return $this->belongsTo(Product::class, 'comment_product_id');
    }
}
