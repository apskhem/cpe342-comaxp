<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOrder extends Model
{
    use HasFactory;

    protected $table = "preorders";
    protected $primaryKey = 'orderNumber';
    public $incrementing = false;

    const UPDATED_AT = null;
    const CREATED_AT = null;

    protected $fillable = [
        'orderNumber',
        'upfrontPrice'
    ];
}
