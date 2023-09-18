<?php

namespace App\Http\Controllers;

use App\Models\Slot;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
class SlotController extends BaseController
{
    //
    //insert and update slot
    public function saveSlot(Request $request){
        try {
        DB::beginTransaction();
        $this->validate($request, [
            'description' => 'required',
            'starttime' => 'required',
            'endtime' => 'required',
            'slotcount' => 'required',
        ]);
        
      
        
       
            if($request->get("id", null)==null){
                 //for new 
                $checkSlottime= Slot::where(function ($query)use ($request) {
                    $query->where('starttime', '=', $request->get("starttime"))
                          ->orWhere('endtime', '=',$request->get("endtime"));
                })->count();
                if($checkSlottime==0){
                        $to_insert = [
                            "description" => $request->get("description", null),
                            "starttime"=> $request->get("starttime", null),
                            "endtime"=>$request->get("endtime", null),
                            "active"=>1,
                            "slotcount"=>$request->get("slotcount", null),
                        ];
                }else{
                    return response()->json(["resultValue" => 'Slot times are already define', 'resultKey' => 0, 'defaultError' => null, 'resultResponse' => 'error'], 200);
                }
            }else{
                //for update
                $to_insert = [
                    "description" => $request->get("description", null),
                    "starttime"=> $request->get("starttime", null),
                    "endtime"=>$request->get("endtime", null),
                    "active"=>$request->get("id", null)==null?1:$request->get("active"),
                    "slotcount"=>$request->get("slotcount", null),
                ];
            }
        
            $slotData = Slot::updateOrCreate(["id" => $request->get("id", null)], $to_insert);
            DB::commit();
            return response()->json(["resultValue" => $slotData, 'resultKey' => 1, 'defaultError' => null, 'resultResponse' => 'error'], 200);

      
            
        } catch (\Exception $e)  {
            DB::rollBack();
            return response()->json(["resultValue" => $e->getMessage(), 'resultKey' => 0, 'defaultError' => null, 'resultResponse' => 'error'], 200);
       
            //throw $th;
        }
    }
    //get all slots
    public function show(Request $request){
        try {
            

            $data= Slot::where('interviewtype',$request->typeid)->get();
            return response()->json(['resultKey' => 1, 'resultValue' => $data, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
        
    }
    public function getAllSlots(Request $request){
        try {
            

            $data= Slot::all();
            return response()->json(['resultKey' => 1, 'resultValue' => $data, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
        
    }
}
