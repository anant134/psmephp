<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\MemberPersonalInformation;
use App\Models\RegistrationType;
use App\Models\MemberType;
use App\Models\MemberRegistrationLog;
use App\Models\EventRegistartion;
use App\Models\EventRegistrationTemp;
use App\Models\EventregistrationLog;
use App\Models\Chapter;
use App\Models\Industry;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller as BaseController;

class MemberController extends BaseController
{
    public function getScanmember(Request $request){
        try {
                $this->validate($request, [
                    'type' => 'required',
                ]);
                $user= auth()->user();
                if($user){
                    $member = DB::select('SELECT m.id,m.name,m.claim,CASE WHEN  
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
         END AS controlnumber,m.memberid FROM psme.members m
join eventregistration e on m.memberid=e.id where claim="'.$request->type.'"');
                    $queryModel = $member;
                    return response()->json(['resultKey' => 1, 'resultValue' => $queryModel, 'errorCode' => null,'errorMsg' => null], 200);
            
                }
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        

    }
    public function SearchMember(Request $request){
        try {
            $filter_key = "";
            $limitless_model = null;
            if ($request->has('search')) {
                $filter_key = trim($request->get('search'));
            }
            $queryModel = DB::table('eventregistration')->where('is_active',1)->where('status_of_transaction',1);
            if ($filter_key) {
                $queryModel = $queryModel->whereRaw('CONCAT_WS("",first_name,middle_name,last_name,email_address,controlnum) like ?', ["%" . $filter_key . "%"]);
            }
            $queryModel = $queryModel->get();
            return response()->json(['resultKey' => 1, 'resultValue' => $queryModel, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
        
    }
    public function updateControlnumber(){
        $limitless_model = null;
        $member = DB::table('registration_temp_personal_information')
            ->whereRaw(('case WHEN registration_temp_personal_information.personal_information_id=1 or registration_temp_personal_information.personal_information_id=5  THEN 
            registration_temp_personal_information.status_of_transaction = "Paid" and registration_temp_personal_information.request_official_receipt="true"
             ELSE true END'))
             ->where('registration_temp_personal_information.controlnum', '=', null)
             ->where('registration_temp_personal_information.is_active', '=', 1)
             ->groupby('registration_temp_personal_information.personal_information_id')->distinct()
            
                       
                        ->orderBy("registration_temp_personal_information.type_of_registration", "asc")
                        ->orderBy("registration_temp_personal_information.personal_information_id", "asc");
                $limitless_model = clone $member;  
                $member=$member->get();
                $type_of_registration=0;
                $counter=0;
               
                for ($i=0; $i < $member->count(); $i++) {
                    if($type_of_registration !=$member[$i]->type_of_registration ) {
                        $type_of_registration =$member[$i]->type_of_registration;
                        if($type_of_registration==1 || $type_of_registration==5){
                            $last = MemberPersonalInformation::where("type_of_registration","=",$type_of_registration )
                                ->where("status_of_transaction","=","Paid" )
                                ->where("request_official_receipt","=","true" )
                                ->whereNotNull('controlnum')->orderBy('personal_information_id', 'DESC')->first();
                        }else{
                            $last = MemberPersonalInformation::where("type_of_registration","=",$type_of_registration )
                                ->whereNotNull('controlnum')
                                ->orderBy('personal_information_id', 'DESC')->first();
                        }
                        
                        $counter=empty($last)?0: $last->controlnum+1;
                    }
                    
                    if(empty($member[$i]->controlnum)){
                        $counter+=1;
                      //  $member[$i]->controlnum=$counter;
                        switch (strlen($counter)) {
                            case 1:
                                $member[$i]->controlnum="00000".$counter;
                            break;
                            case 2:
                                $member[$i]->controlnum="0000".$counter;
                            break;
                            case 3:
                                $member[$i]->controlnum="000".$counter;
                            break;
                            case 4:
                                $member[$i]->controlnum="00".$counter;
                            break;
                            case 5:
                                $member[$i]->controlnum="0".$counter;
                            break;
                            case 6:
                                $member[$i]->controlnum=$counter;
                            break;
                            default:
                                # code...
                                break;
                        }
                        MemberPersonalInformation::where('personal_information_id',$member[$i]->personal_information_id)
                        ->update(['controlnum' => $member[$i]->controlnum]);
                        //::where(["personal_information_id" =>$member[$i]->personal_information_id], $to_insert);
                    }
                    
                }
       
    }
    public function getChartData(Request $request){
        try {
            $cards = DB::select("select count(type_of_registration) as count,type_of_registration,registration_type_of_registration.type_of_registration_description 
            from eventregistration
            left join registration_type_of_registration on registration_type_of_registration.type_of_registration_id=eventregistration.type_of_registration
            where 
                    case WHEN eventregistration.type_of_registration=1 or eventregistration.type_of_registration=5  THEN 
                                eventregistration.status_of_transaction = 1
                                 ELSE true END and eventregistration.is_active=1
                    group by eventregistration.type_of_registration");
       // $queryModel = $cards->get();
        return response()->json(['resultKey' => 1, 'resultValue' => $cards, 'errorCode' => null,'errorMsg' => null], 200);
     
        }  catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
    }
    public function getAllMember(Request $request){
        try {
        $queryModel = DB::table('eventregistration')
        ->leftJoin('registration_type_of_registration', 'registration_type_of_registration.type_of_registration_id', '=', 'eventregistration.type_of_registration')
            ->leftJoin('registration_type_of_membership', 'registration_type_of_membership.type_of_membership_id', '=', 'eventregistration.type_of_membership')
            ->leftJoin('registration_psme_chapter', 'registration_psme_chapter.psme_chapter_id', '=', 'eventregistration.psme_chapter')
           
        ->select('eventregistration.*', 
        'registration_type_of_registration.*',
        'registration_type_of_membership.*',
        'registration_psme_chapter.*',
        DB::raw('upper(concat(eventregistration.first_name," ",
        case when LENGTH(eventregistration.middle_name)>0 then
         Concat(upper(SUBSTRING(eventregistration.middle_name, 1, 1)),".")
         else "" end,
        " ",eventregistration.last_name," ",eventregistration.suffix)) as fullname' ),
        DB::raw('CASE WHEN  
                            eventregistration.type_of_registration = 3 
                       THEN CONCAT("11THPMCH-VSTR-",eventregistration.controlnum)
                       ELSE CONCAT("71STNC-",
                            CASE 
                                WHEN eventregistration.type_of_registration = 1 THEN "DLGT-"  
                                WHEN eventregistration.type_of_registration = 4 THEN "NBOT-"
                                WHEN eventregistration.type_of_registration = 5 THEN "CPRS-"
                                WHEN eventregistration.type_of_registration = 6 THEN "TDCH-"
                                WHEN eventregistration.type_of_registration = 7 THEN "PSTP-"
                                WHEN eventregistration.type_of_registration = 8 THEN "CHRP-"
                                WHEN eventregistration.type_of_registration = 9 THEN "CMMT-"
                                WHEN eventregistration.type_of_registration = 10 THEN "CMMT-"
                                WHEN eventregistration.type_of_registration = 11 THEN "SVCP-"
                            END,  concat(SUBSTRING("000000", 1, (6-LENGTH(eventregistration.controlnum))),eventregistration.controlnum))
                 END AS controlnumber'))
        ->whereRaw(('case WHEN eventregistration.type_of_registration=1 or eventregistration.type_of_registration=5  THEN 
        eventregistration.status_of_transaction = 1
         ELSE true END'))
        ->where('eventregistration.is_active', '=', 1)
        
            ->groupby('eventregistration.id')
            ->distinct('eventregistration.email_address')
            ->orderBy("eventregistration.id", "desc");
            if ($request->has('type')) {
                $filter_key = trim($request->get('type'));
                if(!empty( $filter_key)){
                    $queryModel = $queryModel->where('eventregistration.type_of_registration', '=', $filter_key);
                }
            }
            if ($request->has('eventtype')) {
                $filter_key = trim($request->get('eventtype'));
                if(!empty( $filter_key)){
                    $queryModel = $queryModel->where('eventregistration.eventid', '=', $filter_key);
                }
            }
            $queryModel = $queryModel->get();
            return response()->json(['resultKey' => 1, 'resultValue' => $queryModel, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
    }
    public function getMemberwithlimit(Request $request){
        try {
        $queryModel = DB::table('eventregistration')
        ->leftJoin('registration_type_of_registration', 'registration_type_of_registration.type_of_registration_id', '=', 'eventregistration.type_of_registration')
            ->leftJoin('registration_type_of_membership', 'registration_type_of_membership.type_of_membership_id', '=', 'eventregistration.type_of_membership')
            ->leftJoin('registration_psme_chapter', 'registration_psme_chapter.psme_chapter_id', '=', 'eventregistration.psme_chapter')
           
        ->select('eventregistration.*', 
        'registration_type_of_registration.*',
        'registration_type_of_membership.*',
        'registration_psme_chapter.*',
        DB::raw('upper(concat(eventregistration.first_name," ",
        case when LENGTH(eventregistration.middle_name)>0 then
         Concat(upper(SUBSTRING(eventregistration.middle_name, 1, 1)),".")
         else "" end,
        " ",eventregistration.last_name," ",eventregistration.suffix)) as fullname' ),
        DB::raw('CASE WHEN  
                            eventregistration.type_of_registration = 3 
                       THEN CONCAT("11THPMCH-VSTR-",eventregistration.controlnum)
                       ELSE CONCAT("71STNC-",
                            CASE 
                                WHEN eventregistration.type_of_registration = 1 THEN "DLGT-"  
                                WHEN eventregistration.type_of_registration = 4 THEN "NBOT-"
                                WHEN eventregistration.type_of_registration = 5 THEN "CPRS-"
                                WHEN eventregistration.type_of_registration = 6 THEN "TDCH-"
                                WHEN eventregistration.type_of_registration = 7 THEN "PSTP-"
                                WHEN eventregistration.type_of_registration = 8 THEN "CHRP-"
                                WHEN eventregistration.type_of_registration = 9 THEN "CMMT-"
                                WHEN eventregistration.type_of_registration = 10 THEN "CMMT-"
                                WHEN eventregistration.type_of_registration = 11 THEN "SVCP-"
                            END,  concat(SUBSTRING("000000", 1, (6-LENGTH(eventregistration.controlnum))),eventregistration.controlnum))
                 END AS controlnumber'))
        ->whereRaw(('case WHEN eventregistration.type_of_registration=1 or eventregistration.type_of_registration=5  THEN 
        eventregistration.status_of_transaction = 1
         ELSE true END'))
        ->where('eventregistration.is_active', '=', 1)
        
            ->groupby('eventregistration.id')
            ->distinct('eventregistration.email_address')
            ->orderBy("eventregistration.id", "desc");
            if ($request->has('type')) {
                $filter_key = trim($request->get('type'));
                if(!empty( $filter_key)){
                    $queryModel = $queryModel->where('eventregistration.type_of_registration', '=', $filter_key);
                }
            }
            if ($request->has('eventtype')) {
                $filter_key = trim($request->get('eventtype'));
                if(!empty( $filter_key)){
                    $queryModel = $queryModel->where('eventregistration.eventid', '=', $filter_key);
                }
            }
            $queryModel = $queryModel->skip(0)->take(10);
            $queryModel = $queryModel->get();
            return response()->json(['resultKey' => 1, 'resultValue' => $queryModel, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
    }
    public function getAllMember_old(Request $request){
        try {
            
            $this->updateControlnumber();
       
            $queryModel = DB::table('registration_temp_personal_information')
            ->leftJoin('registration_temp_professional_credentials', 'registration_temp_professional_credentials.personal_information_id', '=', 'registration_temp_personal_information.personal_information_id')
            ->leftJoin('registration_temp_psme_membership_verification', 'registration_temp_psme_membership_verification.personal_information_id', '=', 'registration_temp_personal_information.personal_information_id')
            ->leftJoin('registration_type_of_registration', 'registration_type_of_registration.type_of_registration_id', '=', 'registration_temp_personal_information.type_of_registration')
            ->leftJoin('registration_type_of_membership', 'registration_type_of_membership.type_of_membership_id', '=', 'registration_temp_psme_membership_verification.type_of_membership')
            ->leftJoin('registration_psme_chapter', 'registration_psme_chapter.psme_chapter_id', '=', 'registration_temp_psme_membership_verification.psme_chapter')
           
            ->select('registration_temp_personal_information.*', 
            'registration_temp_professional_credentials.*',
            'registration_temp_psme_membership_verification.*',
            'registration_type_of_registration.*',
            'registration_type_of_membership.*',
            'registration_psme_chapter.*',
            DB::raw('concat(registration_temp_personal_information.first_name," ",
            case when LENGTH(registration_temp_personal_information.middle_name)>0 then
             Concat(upper(SUBSTRING(registration_temp_personal_information.middle_name, 1, 1)),".")
             else "" end,
            " ",registration_temp_personal_information.last_name," ",registration_temp_personal_information.suffix) as fullname' ),
            DB::raw('CASE WHEN  
                            registration_type_of_registration.type_of_registration_id = 3 
                           THEN CONCAT("11THPMCH-VSTR-",registration_temp_personal_information.controlnum)
                           ELSE CONCAT("71STNC-",
                                CASE 
                                    WHEN registration_type_of_registration.type_of_registration_id = 1 THEN "DLGT-"  
                                    WHEN registration_type_of_registration.type_of_registration_id = 4 THEN "NBOT-"
                                    WHEN registration_type_of_registration.type_of_registration_id = 5 THEN "CPRS-"
                                    WHEN registration_type_of_registration.type_of_registration_id = 6 THEN "TDCH-"
                                    WHEN registration_type_of_registration.type_of_registration_id = 7 THEN "PSTP-"
                                    WHEN registration_type_of_registration.type_of_registration_id = 8 THEN "CHRP-"
                                    WHEN registration_type_of_registration.type_of_registration_id = 9 THEN "CMMT-"
                                    WHEN registration_type_of_registration.type_of_registration_id = 10 THEN "CMMT-"
                                    WHEN registration_type_of_registration.type_of_registration_id = 11 THEN "SVCP-"
                                END,  registration_temp_personal_information.controlnum)
                     END AS controlnumber'))
            ->whereRaw(('case WHEN registration_temp_personal_information.personal_information_id=1 or registration_temp_personal_information.personal_information_id=5  THEN 
            registration_temp_personal_information.status_of_transaction = "Paid" and registration_temp_personal_information.request_official_receipt="true"
             ELSE true END'))
            // ->where('registration_temp_personal_information.status_of_transaction', '=', 'Paid')
             ->where('registration_temp_personal_information.is_active', '=', 1)
            ->groupby('registration_temp_personal_information.personal_information_id')
            ->distinct('registration_temp_personal_information.email_address')
            ->orderBy("registration_temp_personal_information.personal_information_id", "desc");
            if ($request->has('type')) {
                $filter_key = trim($request->get('type'));
                if(!empty( $filter_key)){
                    $queryModel = $queryModel->where('registration_temp_personal_information.type_of_registration', '=', $filter_key);
                }
            }
            
            $queryModel = $queryModel->get();
            return response()->json(['resultKey' => 1, 'resultValue' => $queryModel, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
    }
    public function removeDuplicate(Request $request){
       $member= DB::select('select count(*) as cnt,registration_temp_personal_information.email_address,registration_temp_personal_information.contact_number
       from registration_temp_personal_information group by email_address,contact_number
       having count(*)>1');
      // $memberdata=clone $member;
      // $memberdata=$member->get();
        for ($i=0; $i < count($member); $i++) { 
            $memberda= MemberPersonalInformation::where('email_address',$member[$i]->email_address)
            ->where('contact_number',$member[$i]->contact_number)
            ->orderBy('personal_information_id', 'desc')
            ->first();
            MemberPersonalInformation::where('email_address',$member[$i]->email_address)
            ->where('contact_number',$member[$i]->contact_number)
            ->orderBy('personal_information_id', 'asc')
            ->update(['is_active' =>0]);
            MemberPersonalInformation::where('personal_information_id',$memberda->personal_information_id)
            ->update(['is_active' =>1]);
           
        }


        return response()->json(['resultKey' => 1, 'resultValue' => $member, 'errorCode' => null,'errorMsg' => null], 200);
       

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
    public function getMemberType(Request $request){
        try {
            $queryModel = MemberType::query();
            $queryModel = $queryModel->get();
            return response()->json(['resultKey' => 1, 'resultValue' => $queryModel, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
    }
    public function getChapter(Request $request){
        try {
            $queryModel = Chapter::query();
            $queryModel = $queryModel->get();
            return response()->json(['resultKey' => 1, 'resultValue' => $queryModel, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
    }
    public function getIndustry(Request $request){
        try {
            $queryModel = Industry::query();
            $queryModel = $queryModel->get();
            return response()->json(['resultKey' => 1, 'resultValue' => $queryModel, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
    }


    public function deletemember(Request $request){
        try {
            $this->validate($request, [
                'id' => 'required'
            ]);
            $member = EventRegistartion::find($request->id)->update(["is_active"=>0]);
            return response()->json(['resultKey' => 1, 'resultValue' => $member, 'errorCode' => null,'errorMsg' => null], 200);
       
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
    }
    public function updateMember(Request $request){
        try {   
            $this->validate($request, [
                'id' => 'required'
            ]);
            $member = MemberPersonalInformation::find($request->id)->update(
                ["type_of_registration"=>$request->get("type_of_registration", null),
                "status_of_transaction"=>$request->get("status_of_transaction", null)]);
            return response()->json(['resultKey' => 1, 'resultValue' => $member, 'errorCode' => null,'errorMsg' => null], 200);
       
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
    }
    public function saveMemberType(Request $request){
        try {
            $this->validate($request, [
                'facetoface' => 'required',
                'virtual' => 'required',
                'type_of_membership_description' => 'required'
            ]);
            $requestdata= $request->all();
            $to_insert = [
                "facetoface" => $request->get("facetoface", null),
                "virtual"=>$request->get("virtual", null),
                "type_of_membership_description"=>$request->get("type_of_membership_description", null)
            ];
            $membertype;
            if(empty($request->get("id", null))){
                //insert
            }else{
                $membertype = MemberType::where("type_of_membership_id", $request->get("id", null))->first();
                $membertype->facetoface=$request->get("facetoface", null) ;
                $membertype->virtual=$request->get("virtual", null) ;
                $membertype->type_of_membership_description=$request->get("type_of_membership_description", null) ;
                $membertype->save();
              
            }
            //$resdata = MemberType::updateOrCreate(["type_of_membership_id" => $request->get("id", null)], $to_insert);
            return response()->json(['resultKey' => 1, 'resultValue' => $membertype, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
    }

    public function saveMember(Request $request){
        try {
            $requestdata= $request->all();
            $res= json_encode($requestdata);
            $to_insert = [
                "logdata" => $res
            ];
            $resdata = MemberRegistrationLog::updateOrCreate(["id" =>null], $to_insert);
            
            return response()->json(['resultKey' => 1, 'resultValue' => $resdata, 'errorCode' => null,'errorMsg' => null], 200);
      
            //code...
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
    }

    public function saveBulkUpload(Request $request){
        try {
            
            $this->validate($request, [
                'file' => 'required'
            ]);
            $bulkfile=file($request->file);
            $chunks = array_chunk($bulkfile,10000);
            
            foreach ($chunks as $key => $chunk) {
                $data = array_map('str_getcsv', $chunk);
                    if($key == 0){
                        $header = $data[0];
                        unset($data[0]);
                    }
                    $this->bulkupload($data);
                   // ItemCSVUploadJob::dispatch($data, $header);                
            }
            // $count=1;
            // $countasdasd=50;
            // $intakesFormObjects = [];
            // $to_insertlog=[];
            // foreach ($data as $key => $val) {
                
            //     if($count==$countasdasd){
            //         $countasdasd=$countasdasd+50;
            //     }
                
              
            //     if($data[$key][18]){
            //         // $queryModel = DB::table('eventregistration_temp')
                
            //         // ->where('eventregistration_temp.email_address', $data[$key][9])
            //         // ->orWhere('eventregistration_temp.contact_number', $data[$key][8])
            //         // ->orWhere('eventregistration_temp.prc_license_number', $data[$key][18]);
            //         // $queryModel = $queryModel->get();
            //         // if(count($queryModel)){
                       
            //         // }else{
            //             // $qry="select count(*) as cntnumber 
            //             // from eventregistration_temp where status_of_transaction=1 
            //             // and is_active=1  and controlnum is not null
            //             // and type_of_registration=".$data[$key][18];
            //             // $resqry= DB::select($qry);
            //             // $cntnumber=$resqry[0]->cntnumber+1;
            //             $dataobj=[
            //                 "first_name"=>$data[$key][0],
            //                 "middle_name"=>$data[$key][1],
            //                 "last_name"=>$data[$key][2],
            //                 "suffix"=>$data[$key][3],
            //                  "gender"=>$data[$key][4],
            //                  "birth_date"=>$data[$key][5],
            //                  "complete_address"=>$data[$key][6],
            //                  "zip_code"=>$data[$key][7],
            //                  "contact_number"=>$data[$key][8],
            //                  "email_address"=>$data[$key][9],
            //                  "sector"=>$data[$key][10],
            //                 "company_name"=>$data[$key][11],
            //                 "job_title"=>$data[$key][12],
            //                 "industry"=>$data[$key][13],
            //                 "type_of_registration"=>$data[$key][14],
            //                 "status_of_transaction"=>1,
            //                 "eventtype"=>$data[$key][16],
            //                 "eventid"=>$data[$key][17],
            //                 "prc_license_number"=>$data[$key][18],
            //                 "prc_license_date_of_registration"=>$data[$key][19],
            //                 "prc_license_date_of_expiration"=>$data[$key][20],
            //                 "type_of_membership"=>$data[$key][21],
            //                 "psme_chapter"=>$data[$key][22],
                           
            //                 "isbulkuploaded"=>1,
            //             ];

            //             array_push($to_insertlog, $dataobj);
            //             $to_insertlog[] = $dataobj;
            //             $count=$count+1;
                        
            //        // }
            //     }
                
            // }
            



            // DB::beginTransaction();
            // $intakesFormObjects[]= EventRegistrationTemp::insert($to_insertlog);
            //             //EventRegistrationTemp::updateOrCreate(["id" => null], $to_insertlog);
                        
            //             DB::commit();
           
           
           

            //code...
           
                return response()->json(['resultKey' => 1, 'resultValue' => [], 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
           
    }

    public function bulkupload($data){
        $intakesFormObjects = [];
        $to_insertlog=[];
        foreach ($data as $key => $val) {
            //if($data[$key][18]){
                        $dataobj=[
                            "first_name"=>$data[$key][0],
                            "middle_name"=>$data[$key][1],
                            "last_name"=>$data[$key][2],
                            "suffix"=>$data[$key][3],
                             "gender"=>$data[$key][4],
                             "birth_date"=>$data[$key][5],
                             "complete_address"=>$data[$key][6],
                             "zip_code"=>$data[$key][7],
                             "contact_number"=>$data[$key][8],
                             "email_address"=>$data[$key][9],
                             "sector"=>$data[$key][10],
                            "company_name"=>$data[$key][11],
                            "job_title"=>$data[$key][12],
                            "industry"=>$data[$key][13],
                            "type_of_registration"=>$data[$key][14],
                            "status_of_transaction"=>1,
                            "eventtype"=>$data[$key][16],
                            "eventid"=>$data[$key][17],
                            "prc_license_number"=>$data[$key][18],
                            "prc_license_date_of_registration"=>$data[$key][19],
                            "prc_license_date_of_expiration"=>$data[$key][20],
                            "type_of_membership"=>$data[$key][21],
                            "psme_chapter"=>$data[$key][22],
                           
                            "isbulkuploaded"=>1,
                        ];
                        array_push($to_insertlog, $dataobj);
                       // $to_insertlog[] = $dataobj;
                
              //  }
                
        }
        DB::beginTransaction();
        
            $intakesFormObjects[]= EventRegistartion::insert($to_insertlog);
            
        DB::commit();
        //control num
        $eventreg=EventRegistartion::where("is_active",1)->where("isbulkuploaded",1)->where("controlnum",null);

        $eventreg=$eventreg->get();
        $evenRegistype= DB::select('select count(*) as cnt,type_of_registration
        from eventregistration where is_active=1 and isbulkuploaded=1  group by type_of_registration');
        

        foreach ($evenRegistype as $key => $value) {
            $cnt=1;
            $qry=DB::select("select controlnum 
            from eventregistration where status_of_transaction=1 
            and is_active=1  and controlnum is not null
            and type_of_registration=".$evenRegistype[$key]->type_of_registration." order by controlnum desc limit 1");
            if($qry){
                $cnt=$qry[0]->controlnum+1;
            }
            
            $s="";
            foreach ($eventreg as $key => $value) {
                $eventreg[$key]->controlnum=$cnt;
                $eventreg[$key]->save();
                $cnt=$cnt+1;
            }
            # code...
        }

        
       
        


    }


    public function updateMemberControlnumber(Request $request)
    {
        $event=EventRegistartion::where('is_active',1)->where("type_of_registration",3)->orderBy("created_at", "asc");
        $event=$event->get();
        for ($i=0; $i < count($event); $i++) {
            $updatevent=EventRegistartion::find($event[$i]->id);
            $updatevent->controlnum=$i+1;
            $updatevent->save();
        }
        return response()->json(['resultKey' => 1, 'resultValue' => 1, 'errorCode' => null,'errorMsg' => null], 200);
      
    }
   

    public function updateMemberalldata(Request $request){
        try {   
            $this->validate($request, [
                'id' => 'required'
            ]);
            
        DB::beginTransaction();
           $en= [   
                "first_name"=>$request->get("first_name", null),
                "middle_name"=>$request->get("middle_name", null),
                "last_name"=>$request->get("last_name", null),
                "suffix"=>$request->get("suffix", null),
                "gender"=>$request->get("gender", null),
                "birth_date"=>$request->get("birth_date", null),
                "complete_address"=>$request->get("complete_address", null),
                "zip_code"=>$request->get("zip_code", null),
                "contact_number"=>$request->get("contact_number", null),
                "email_address"=>$request->get("email_address", null),
                "sector"=>$request->get("sector", null),
                "company_name"=>$request->get("company_name", null),
                "job_title"=>$request->get("job_title", null),
                "industry"=>$request->get("industry", null),
               // "type_of_registrant"=>$request->get("type_of_registrant", null),
                //"type_of_registration"=>$request->get("type_of_registration", null),
                "prc_license_number"=>$request->get("prc_license_number", null),
                "prc_license_date_of_registration"=>$request->get("prc_license_date_of_registration", null),
                "prc_license_date_of_expiration"=>$request->get("prc_license_date_of_expiration", null),
                "pwd_id_number"=>$request->get("pwd_id_number", null),
              //  "type_of_membership"=>$request->get("type_of_membership", null),
                "psme_chapter"=>$request->get("psme_chapter", null),
                "prc_sequence_number"=>$request->get("prc_sequence_number", null),
                "month_passed"=>$request->get("month_passed", null)
                
            ];

            $member = EventRegistartion::find($request->id)->update($en);
            $en["eventregid"]=$request->id;
            $en["updatefrom"]="Member Edit";
            $eventchangelog=EventregistrationLog::updateOrCreate(["id" =>null], $en);
            $memberinfo = EventRegistartion::find($request->id);
            DB::commit();

            return response()->json(['resultKey' => 1, 'resultValue' =>$memberinfo, 'errorCode' => null,'errorMsg' => null], 200);
       
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
        
    }

}