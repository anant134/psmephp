<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\MemberPersonalInformation;
use App\Models\RegistrationType;
use App\Models\MemberType;
use App\Models\MemberRegistrationLog;
use App\Models\EventRegistartion;
use Illuminate\Support\Facades\DB;
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
            $queryModel = DB::table('eventregistration');
            if ($filter_key) {
                $queryModel = $queryModel->whereRaw('CONCAT_WS("",first_name,middle_name,last_name,email_address,prc_license_number,controlnum) like ?', ["%" . $filter_key . "%"]);
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
                            END,  eventregistration.controlnum)
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
    public function deletemember(Request $request){
        try {
            // $this->validate($request, [
            //     'id' => 'required'
            // ]);
            // $member = MemberPersonalInformation::find($request->id)->update(["is_active"=>0]);
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
            DB::beginTransaction();
            $this->validate($request, [
                'bulkuploadfile' => 'required'
            ]);
            $bulkfile=$request->get('bulkuploadfile', null);

            for ($i=0; $i < count($bulkfile); $i++) {
                $bulkfile[$i]['bulkstatus']=false;
                $bulkfile[$i]['bulkreason']=""; 
                $bfile=$bulkfile[$i];
                //check if exist
                $queryModel = DB::table('eventregistration')
                
                ->where('eventregistration.email_address', $bfile['email_address'])
                ->orWhere('eventregistration.contact_number', $bfile['contact_number'])
                ->orWhere('eventregistration.prc_license_number', $bfile['prc_license_number']);
                $queryModel = $queryModel->get();
                if(count($queryModel)){
                    $bulkfile[$i]['bulkstatus']=false;
                    $bulkfile[$i]['bulkreason']="Already exist";
                }else{
                    //get control number 
                    $qry="select count(*) as cntnumber 
                    from eventregistration where status_of_transaction=1 
                    and is_active=1 
                    and type_of_registration=".$bfile['type_of_registration'];
                    $resqry= DB::select($qry);
                    $cntnumber=$resqry[0]->cntnumber+1;
                    $to_insertlog = [
                        "first_name"=>$bulkfile[$i]['first_name'],
                        "middle_name"=>$bulkfile[$i]['middle_name'],
                        "last_name"=>$bulkfile[$i]['last_name'],
                        "suffix"=>$bulkfile[$i]['suffix'],
                        "gender"=>$bulkfile[$i]['gender'],
                        "birth_date"=>$bulkfile[$i]['birth_date'],
                        "complete_address"=>$bulkfile[$i]['complete_address'],
                        "zip_code"=>$bulkfile[$i]['zip_code'],
                        "contact_number"=>$bulkfile[$i]['contact_number'],
                        "email_address"=>$bulkfile[$i]['email_address'],
                        "sector"=>$bulkfile[$i]['sector'],
                        "company_name"=>$bulkfile[$i]['company_name'],
                        "job_title"=>$bulkfile[$i]['job_title'],
                        "industry"=>$bulkfile[$i]['industry'],
                        "type_of_registrant"=>$bulkfile[$i]['type_of_registrant'],
                        "type_of_registration"=>$bulkfile[$i]['type_of_registration'],
                        "status_of_transaction"=>1,
                        "eventtype"=>$bulkfile[$i]['eventtype'],
                        "eventid"=>$bulkfile[$i]['eventid'],
                        "prc_license_number"=>$bulkfile[$i]['prc_license_number'],
                        "prc_license_date_of_registration"=>$bulkfile[$i]['prc_license_date_of_registration'],
                        "prc_license_date_of_expiration"=>$bulkfile[$i]['prc_license_date_of_expiration'],
                        "pwd_id_number"=>$bulkfile[$i]['pwd_id_number'],
                        "type_of_membership"=>$bulkfile[$i]['type_of_membership'],
                        "psme_chapter"=>$bulkfile[$i]['psme_chapter'],
                        "prc_sequence_number"=>$bulkfile[$i]['prc_sequence_number'],
                        "month_passed"=>$bulkfile[$i]['month_passed'],
                        "controlnum"=>$cntnumber,
                        "isbulkuploaded"=>1,
                    ];
                     $eventre= EventRegistartion::updateOrCreate(["id" => null], $to_insertlog);
                     $bulkfile[$i]['bulkstatus']=true;
                     $bulkfile[$i]['bulkreason']="";
                }
                //if not add member
                

            }

            //code...
            DB::commit();
                return response()->json(['resultKey' => 1, 'resultValue' => $bulkfile, 'errorCode' => null,'errorMsg' => null], 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
           



    }



   

}