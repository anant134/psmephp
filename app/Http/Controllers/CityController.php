<?php

namespace App\Http\Controllers;

use App\Models\City;
use Laravel\Lumen\Routing\Controller as BaseController;

class CityController extends BaseController
{
    //

    public function show(){
        try {
            $data= City::all();
            return response()->json(['resultKey' => 1, 'resultValue' => $data, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
        
    }
   
}
