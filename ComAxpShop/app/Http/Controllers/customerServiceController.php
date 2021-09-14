<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class customerServiceController extends Controller
{
    public function getProductsCatalog() {

        $products = DB::table('products')
        ->groupBy('productLine')
        ->get(['productLine','productName']);
        
        return $products;
    }
}

// $customers = DB::table('customers')
// ->join('employees', 'customers.salesRepEmployeeNumber', '=', 'employees.employeeNumber')
// ->where('employees.employeeNumber', $id)
// ->get(['customerNumber', 'customerName', 'salesRepEmployeeNumber', 'employeeNumber', 'lastName', 'firstName', 'jobTitle']);
// return $customers;
