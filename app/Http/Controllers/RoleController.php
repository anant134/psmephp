<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Roleoption;
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
    public function getRoleoption(Request $request){
        try {
           
            $user= auth()->user();

            if($user){
                if($user->account_id==0){
                    $where=array();
                    $where[] = ['is_active', '==', 1];
                    $queryModel = Roleoption::where('is_active', '=', 1)->with('menu')->get();
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
                if(!is_null($queryModel->appaccesscontrol)){
                    $queryModel->appaccesscontrol=json_decode($queryModel->appaccesscontrol);
                }
                if(!is_null($queryModel->webaccesscontrol)){
                    $queryModel->webaccesscontrol=json_decode($queryModel->webaccesscontrol);
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
                'rolename' => 'required'
            ]);
            $accessdetail= $request->get("appmenu", null);
            $menuitemdetail= $request->get("menuitem", null);
            $to_insert = [
                
                "name" => $request->get("rolename", null)
            ];
            $role = Role::updateOrCreate(["id" =>$request->get("id", $request->get("roleid", null))], $to_insert);
            $role->appaccesscontrol=json_encode($accessdetail) ;
            $role->webaccesscontrol=json_encode($menuitemdetail) ;
            $role->save();
            DB::commit();
            return response()->json(['resultKey' => 1, 'resultValue' => $role, 'errorCode' => null,'errorMsg' => null], 200);
    
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
    }
    
}
