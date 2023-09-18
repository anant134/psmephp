<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Fee;
use App\Models\Country;
use Laravel\Lumen\Routing\Controller as BaseController;

class CommonController extends BaseController
{
    public function getCommonMaster(Request $request){
        try {
            $feedata= Fee::all();
            $countrydata= Country::all();
            $result=["fee"=>$feedata,"country"=>$countrydata];
            return response()->json(['resultKey' => 1, 'resultValue' => $result, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
        
    }
}