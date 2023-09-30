<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use Laravel\Lumen\Routing\Controller as BaseController;

class RoleController extends BaseController
{
    // anant

    public function getRole(Request $request){
        try {
           
            $user= auth()->user();

            if($user){
                if($user->account_id==0){
                    $where=array();
                    $where[] = ['is_active', '==', 1];
                    $queryModel = Role::where('is_active', '=', 1)->whereNotIn('id', [1,2])->get();
                   // $queryModel = Role::where('id', '!=' , 0);
                    return response()->json(['resultKey' => 1, 'resultValue' => $queryModel, 'errorCode' => null,'errorMsg' => null], 200);
            
                }else{
                    $queryModel=Role::where('account_id',$user->account_id)
                    ->get();
                    return response()->json(['resultKey' => 1, 'resultValue' => $queryModel, 'errorCode' => null,'errorMsg' => null], 200);
            
                }
            }else{
                return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => "Token expire"], 200);
        
            }

           
            
          
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
    }
    public function getRoleByID(Request $request){
        try {
            $this->validate($request, [
                'id' => 'required'
            ]);
            $queryModel=Role::where('id',$request->get("id", null))
            ->first();
            if($queryModel){
                if(!is_null($queryModel->accessdetail)){
                    $queryModel->accessdetail=json_decode($queryModel->accessdetail);
                }
                return response()->json(['resultKey' => 1, 'resultValue' => $queryModel, 'errorCode' => null,'errorMsg' => null], 200);
            }else{
                return response()->json(['resultKey' => 1, 'resultValue' => null, 'errorCode' => null,'errorMsg' => null], 200);
        
            }
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
    }
    public function addUpdateRole(Request $request){
        try {
            DB::beginTransaction();
            $this->validate($request, [
                'accessdetail' => 'required',
                'name' => 'required'
            ]);
            $accessdetail= $request->get("accessdetail", null);
            $to_insert = [
                
                "name" => $request->get("name", null)
            ];
            $role = Role::updateOrCreate(["id" =>$request->get("id", null)], $to_insert);
            $role->accessdetail=$accessdetail;
            $role->save();
            DB::commit();
            return response()->json(['resultKey' => 1, 'resultValue' => $role, 'errorCode' => null,'errorMsg' => null], 200);
    
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
    }
    
}
