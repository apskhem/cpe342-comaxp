<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    use HasFactory;

    protected $table = "discountcodes";
    protected $primaryKey = 'discountCode';

    const UPDATED_AT = null;
    const CREATED_AT = null;

    protected $fillable = [
        'discountCode',
        'startDate',
        'endDate',
        'amount',
        'discountPrice'
    ];
}
