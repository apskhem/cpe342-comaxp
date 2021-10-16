<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    use HasFactory;
    protected $table = "products";

    protected $primaryKey = 'productCode';
    
    const UPDATED_AT = null;
    const CREATED_AT = null;

    public $incrementing = false;

    protected $fillable = [
        'productCode',
        'productName',
        'productLine',
        'productScale',
        'productVendor',
        'productDescription',
        'quantityInStock',
        'buyPrice',
        'MSRP'
    ];
}
