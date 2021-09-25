<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\Token;

class Employee extends Authenticatable{
    use HasFactory, Notifiable;
    use HasApiTokens;

    protected $table = "employees";
    protected $primaryKey = 'employeeNumber';

    const UPDATED_AT = null;
    const CREATED_AT = null;

    protected $fillable = [
        'lastName',
        'firstName',
        'extension',
        'email',
        'officeCode',
        'reportsTo',
        'jobTitle',
        'password'
    ];
}
