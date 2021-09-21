<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Session;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class authController extends Controller {

    public function index() {
        return view('auth.login');
    }  
      
    public function customLogin(Request $request)
    {
        $request->validate([
            'employeeNumber' => 'required',
            'password' => 'required',
        ]);
   
        $credentials = $request->only('employeeNumber', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->intended('dashboard')->withSuccess('Signed in');
        }
  
        return redirect("login")->withSuccess('Login details are not valid');
    }



    public function registration() {
        return view('auth.registration');
    }
      

    public function customRegistration(Request $request) {

        $request->validate([
            'employeeNumber' => 'required|digits:4|unique:employees|',
            'lastName' => 'required',
            'firstName' => 'required',
            'extension' => 'required',
            'email' => 'required|email|unique:employees',
            'officeCode' => 'required',
            'reportsTo' => 'required|exists:employees,employeeNumber',
            'jobTitle' => 'required',
            'password' => 'required|min:6',
        ]);
           
        $employeeData = $request->all();
        $fetch = $this->createEmployee($employeeData);
         
        return redirect("dashboard")->with('success','register success');
    }


    public function createEmployee(array $employeeData)
    {
      return Employee::create([
        'employeeNumber' => $employeeData['employeeNumber'],
        'lastName' => $employeeData['lastName'],
        'firstName' => $employeeData['firstName'],
        'extension' => $employeeData['extension'],
        'email' => $employeeData['email'],
        'officeCode' => $employeeData['officeCode'],
        'reportsTo' => $employeeData['reportsTo'],
        'jobTitle' => $employeeData['jobTitle'],
        'password' => bcrypt($employeeData['password'])
      ]);
    }    
    

    public function dashboard()
    {
        if(Auth::check()){
            return view('dashboard');
        }
  
        return redirect("login")->withSuccess('You are not allowed to access');
    }
    

    public function signOut() {
        Session::flush();
        Auth::logout();
  
        return Redirect('login');
    }
}