<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Member;
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

   

}