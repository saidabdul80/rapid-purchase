<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function setData(Request $request){
        $type = $request->get('type'); //for domain
        $name = $request->get('name');
        $value = $request->get('value');
        $key = $type.'_'.$name;
        session([$key=> $value]);        
        return 200;
    }

    public function fitbitRedirect(){
        return view('fitbit');
    }
}
