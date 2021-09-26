<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDatail extends Model
{
    use HasFactory;

    protected $table = "orderdetails";
    protected $primaryKey = 'orderNumber';

    const UPDATED_AT = null;
    const CREATED_AT = null;

    protected $fillable = [
        'orderNumber',
        'productCode',
        'quantityOrdered',
        'priceEach',
        'orderLineNumber',
    ];
}

