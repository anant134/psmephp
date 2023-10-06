<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberClaimLog;
use App\Models\AppSetting;
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
                'member_id' => 'required',
                'claim' => 'required',
            ]);
           
            $member_id=$request->get("member_id", null);
            $memberdata=Member::where('member_id',$member_id);
            $claim=$request->get("claim", null);
            switch ($claim) {
                case 'checkin':
                    $to_insert = [
                        "checkin" =>Carbon::now()
                    ];
                    if(!empty( $memberdata->checkin)){
                        return ['resultKey' => 0, 'resultValue' => 
                        null, 'errorCode' => 'err1', 'defaultError' => 'Already Checkin'];
                    }
                break;
                case 'checkout':
                    $to_insert = [
                        "checkout" =>Carbon::now()
                    ];
                    if(!empty( $memberdata->checkout)){
                        return ['resultKey' => 0, 'resultValue' => 
                        null, 'errorCode' => 'err1', 'defaultError' => 'Already Checkout'];
                    }
                break;
                case 'exhibit':
                    $to_insert = [
                        "exhibit" =>true
                    ];
                    if(!empty( $memberdata->exhibit)){
                        return ['resultKey' => 0, 'resultValue' => 
                        null, 'errorCode' => 'err1', 'defaultError' => 'Already Claimed'];
                    }
                break;
                case 'souveneir':
                    $to_insert = [
                     "souveneir" =>true
                    ];
                    if(!empty( $memberdata->souveneir)){
                        return ['resultKey' => 0, 'resultValue' => 
                        null, 'errorCode' => 'err1', 'defaultError' => 'Already Claimed'];
                    }
                break;
                case 'food':
                    $to_insert = [
                       "food" =>true
                    ];
                    if(!empty( $memberdata->food)){
                        return ['resultKey' => 0, 'resultValue' => 
                        null, 'errorCode' => 'err1', 'defaultError' => 'Already Claimed'];
                    }
                    $foodcount=MemberClaimLog::where('claimfor','food')->count();
                    $foodlimit=AppSetting::all()->first()->foodcount;
                    if($foodcount>=$foodlimit){
                        return ['resultKey' => 0, 'resultValue' => 
                        null, 'errorCode' => 'err1', 'defaultError' => 'Reach the limit'];
                     
                    }

                   
                break;

                default:
                    # code...
                    break;
            }
            
            $member= Member::updateOrCreate(["id" => $member_id], $to_insert);
            $to_insertlog = [
                "claimfor" =>$claim,
                "member_id"=>$member_id,
            ];
            MemberClaimLog::updateOrCreate(["id" => null],$to_insertlog);
           
           
           
            return response()->json(['resultKey' => 1, 'resultValue' => $member, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
        
    }
   
}
