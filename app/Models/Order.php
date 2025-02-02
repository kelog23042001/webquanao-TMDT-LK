<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'user_id', 'shipping_id', 'order_date', 'order_status', 'created_at', 'total'
    ];
    protected $primaryKey = 'order_id';
    protected $table = 'tbl_order';

    public function Orders()
    {
        return $this->belongsTo(User::class);
    }
}
