<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\City;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class ProvinceController extends BaseController
{
    //

    public function show(){
        try {
            $data=Province::all();
            return response()->json(['resultKey' => 1, 'resultValue' => $data, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
        
    }
    public function getCityByProvinceId(Request $request){
        try {
            $queryModel = City::query();
            if ($request->has("province_id")) {
                $queryModel = $queryModel
                    ->where("province_id", $request->get("province_id"));
                    $queryModel->orderBy("name", "asc");
            }
            $queryModel = $queryModel->get();
            if ($queryModel->count()) {
                
                $queryModel = $queryModel->toArray();
            }
            return response()->json(['resultKey' => 1, 'resultValue' => $queryModel, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
        
    }
    public function getProvinceByCountryId(Request $request){
        try {
            $queryModel = Province::query();
            if ($request->has("country_id")) {
                $queryModel = $queryModel
                    ->where("country_id", $request->get("country_id"));
                    $queryModel->orderBy("name", "asc");
            }
            $queryModel = $queryModel->get();
            if ($queryModel->count()) {
                
                $queryModel = $queryModel->toArray();
            }
            return response()->json(['resultKey' => 1, 'resultValue' => $queryModel, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
        
    }
    public function getCity(Request $request){
        try {
           // $data = City::find( 1);
            $where=array();
            $where[] = ['province_id', '=', $request->get('provinceid')];
            $results = City::where($where)->get();
            return response()->json(['resultKey' => 1, 'resultValue' => $results, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
        
    }
}
