<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Fee;
use Laravel\Lumen\Routing\Controller as BaseController;

class FeeController extends BaseController
{
    public function getFee(Request $request){
        try {
            $data= Fee::all();
            return response()->json(['resultKey' => 1, 'resultValue' => $data, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
        
    }
}