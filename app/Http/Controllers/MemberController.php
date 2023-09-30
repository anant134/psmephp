<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\MemberPersonalInformation;
use App\Models\RegistrationType;
use Laravel\Lumen\Routing\Controller as BaseController;

class MemberController extends BaseController
{
    public function SearchMember(Request $request){
        try {
            $filter_key = "";
            $limitless_model = null;
            if ($request->has('search')) {
                $filter_key = trim($request->get('search'));
            }
            $queryModel = Member::query();
            if ($filter_key) {
                $queryModel = $queryModel->whereRaw('CONCAT_WS("",name,controlnumber) like ?', ["%" . $filter_key . "%"]);
            }
            $queryModel = $queryModel->get();
            return response()->json(['resultKey' => 1, 'resultValue' => $queryModel, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
        
    }
    public function getAllMember(Request $request){
        try {

            $queryModel = MemberPersonalInformation::query();
            if ($request->has('type')) {
                $filter_key = trim($request->get('type'));
                if($filter_key!=""){
                    $queryModel = $queryModel->with('registration')
                    ->where('status_of_transaction', '=', 'Paid')
                    ->where('type_of_registration', '=', $filter_key)
                    ->orderBy("personal_information_id", "desc");
                }else{
                    $queryModel = $queryModel->with('registration')
                    ->where('status_of_transaction', '=', 'Paid')
                    ->orderBy("personal_information_id", "desc");
                }
            
            }else{
                $queryModel = $queryModel->with('registration')
                ->where('status_of_transaction', '=', 'Paid')
                ->orderBy("personal_information_id", "desc");
            }
            



            
            $queryModel = $queryModel->get();
            return response()->json(['resultKey' => 1, 'resultValue' => $queryModel, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
    }

    public function getRegistrationType(Request $request){
        try {
            $queryModel = RegistrationType::query();
            $queryModel = $queryModel->get();
            return response()->json(['resultKey' => 1, 'resultValue' => $queryModel, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
    }

   

}