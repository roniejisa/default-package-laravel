<?php
namespace ExampleRS\Controllers;

use ExampleHelper;
use Illuminate\Http\Request;

class ExampleController
{
    public function index(Request $request){
        
        return view('ers::index');
    }
}