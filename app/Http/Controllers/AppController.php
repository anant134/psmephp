<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberClaimLog;
use App\Models\AppSetting;
use App\Models\EventRegistartion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller as BaseController;
use Validator;
use Carbon\Carbon;

class AppController extends BaseController
{
    //

    public function claim(Request $request){
        try {
            $this->validate($request, [
                'id' => 'required',
                'claim' => 'required',
            ]);
           
            $id=$request->get("id", null);
            $eventdata=EventRegistartion::where("id",$id)->where("is_active",1)->first();
            if(empty($eventdata)){
                return ['resultKey' => 0, 'resultValue' => 
                        null, 'errorCode' => 'err1', 'defaultError' => 'Member not found'];
            }else{
                $claim=$request->get("claim", null);
                
                $member= Member::where("memberid",$id)->where("claim",$claim);
                if($member->count()>0){
                    return ['resultKey' => 0, 'resultValue' => 
                    null, 'errorCode' => 'err1', 'defaultError' => 'Already Claim'];
                }else{
                    $name=$eventdata->first_name." ".$eventdata->middle_name." ".$eventdata->last_name ;
                    if ($claim=="food") {
                            $foodcount=  Member::where("claim",$claim)->get();
                            $foodcount=count($foodcount);
                            $foodlimit=AppSetting::all()->first()->foodcount;
                            if($foodcount>$foodlimit){
                                return ['resultKey' => 0, 'resultValue' => 
                                null, 'errorCode' => 'err1', 'defaultError' => 'Reach the limit'];
                            }else{
                                $member= Member::updateOrCreate(["id" => null], [
                                    "claim"=>$claim, 
                                    "memberid"=>$id,
                                    "controlnumber"=>$eventdata->controlnum,
                                    "name"=>$name]);
                                    $to_insertlog = [
                                        "claimfor" =>$claim,
                                        "member_id"=>$id,
                                    ];
                                    MemberClaimLog::updateOrCreate(["id" => null],$to_insertlog);
                            }
                    }else{
                        $member= Member::updateOrCreate(["id" => null], [
                            "claim"=>$claim, 
                            "memberid"=>$id,
                            "controlnumber"=>$eventdata->controlnum,
                            "name"=>$name]);
                            $to_insertlog = [
                                "claimfor" =>$claim,
                                "member_id"=>$id,
                            ];
                            MemberClaimLog::updateOrCreate(["id" => null],$to_insertlog);
                    }
                }

                $memberclaim = DB::select('SELECT m.*,CASE WHEN  
                    e.type_of_registration = 3 
               THEN CONCAT("11THPMCH-VSTR-",m.controlnumber)
               ELSE CONCAT("71STNC-",
                    CASE 
                        WHEN e.type_of_registration = 1 THEN "DLGT-"  
                        WHEN e.type_of_registration = 4 THEN "NBOT-"
                        WHEN e.type_of_registration = 5 THEN "CPRS-"
                        WHEN e.type_of_registration = 6 THEN "TDCH-"
                        WHEN e.type_of_registration = 7 THEN "PSTP-"
                        WHEN e.type_of_registration = 8 THEN "CHRP-"
                        WHEN e.type_of_registration = 9 THEN "CMMT-"
                        WHEN e.type_of_registration = 10 THEN "CMMT-"
                        WHEN e.type_of_registration = 11 THEN "SVCP-"
                    END,  concat(SUBSTRING("000000", 1, (6-LENGTH(m.controlnumber))),m.controlnumber))
         END AS pcontrolnumber FROM psme.members m
join eventregistration e on m.memberid=e.id where m.id="'.$member->id.'"');


            }
          
            return response()->json(['resultKey' => 1, 'resultValue' => $memberclaim, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
        
    }
   
}
