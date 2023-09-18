<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Country;
use Laravel\Lumen\Routing\Controller as BaseController;

class CountryController extends BaseController
{
    public function getCountry(Request $request){
        try {
            $data= Country::all();
            return response()->json(['resultKey' => 1, 'resultValue' => $data, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
        
    }
}