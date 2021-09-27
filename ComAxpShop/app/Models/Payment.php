<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = "payments";
    // protected $primaryKey = ['customerNumber', 'checkNumber'];
    protected $primaryKey = 'orderNumber';

    const UPDATED_AT = null;
    const CREATED_AT = null;

    protected $fillable = [
        'customerNumber',
        'checkNumber',
        'paymentDate',
        'amount',
        'orderNumber',
    ];
}
