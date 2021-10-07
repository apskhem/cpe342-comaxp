<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\OrderDetail;

class Order extends Model
{
    use HasFactory;

    protected $table = "orders";
    protected $primaryKey = 'orderNumber';

    const UPDATED_AT = null;
    const CREATED_AT = null;

    protected $fillable = [
        'orderDate',
        'requiredDate',
        'shippedDate',
        'status',
        'comments',
        'customerNumber',
        'discountCode',
    ];

    public function orderdetail(){
        return $this->hasMany(OrderDetail::class, 'orderNumber');
    }
}

