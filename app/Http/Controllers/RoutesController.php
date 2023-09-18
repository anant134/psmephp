<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Routes;
use Laravel\Lumen\Routing\Controller as BaseController;

class RoutesController extends BaseController
{
    // anant

    public function getRoutes(Request $request){
        try {
           
            $user= auth()->user();

            if($user){
                if($user->account_id==0){
                    $where=array();
                    $where[] = ['account_id', '!=', 0];
                    $queryModel = Routes::where($where)->get();
                    for ($i=0; $i <count($queryModel) ; $i++) { 
                        $queryModel[$i]["routeaccess"]= json_decode($queryModel[$i]["routeaccess"]);
                        $queryModel[$i]["roles"]=["add"=>0,"edit"=>0,"delete"=>0,"view"=>0,"print"=>0];
                    }
                    return response()->json(['resultKey' => 1, 'resultValue' => $queryModel, 'errorCode' => null,'errorMsg' => null], 200);
            
                }else{
                    $queryModel=Routes::where('account_id',$user->account_id)
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
    public function getRoutesByID(Request $request){
        try {
            $this->validate($request, [
                'id' => 'required'
            ]);
            $queryModel=Routes::where('id',$request->get("id", null))
            ->first();
            if($queryModel){
                if(!is_null($queryModel->routeaccess)){
                    $queryModel->routeaccess=json_decode($queryModel->routeaccess);
                }
                return response()->json(['resultKey' => 1, 'resultValue' => $queryModel, 'errorCode' => null,'errorMsg' => null], 200);
            }else{
                return response()->json(['resultKey' => 1, 'resultValue' => null, 'errorCode' => null,'errorMsg' => null], 200);
        
            }
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
    }
    public function addUpdateRoutes(Request $request){
        try {
            DB::beginTransaction();
            $this->validate($request, [
                'routeaccess' => 'required',
                'name' => 'required'
            ]);
            $routeaccess= $request->get("routeaccess", null);
            $to_insert = [
                
                "name" => $request->get("name", null)
            ];
            $routes = Routes::updateOrCreate(["id" =>$request->get("id", null)], $to_insert);
            $routes->routeaccess=$routeaccess;
            $routes->save();
            DB::commit();
            return response()->json(['resultKey' => 1, 'resultValue' => $role, 'errorCode' => null,'errorMsg' => null], 200);
    
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
    }
}
