<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\MemberPersonalInformation;
use App\Models\RegistrationType;

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
            DB::raw('concat(registration_temp_personal_information.first_name," ",registration_temp_personal_information.middle_name," ",registration_temp_personal_information.last_name," ",registration_temp_personal_information.suffix) as fullname' ),
            DB::raw('CASE WHEN  registration_type_of_registration.type_of_registration_id = 3 THEN "11THPMCH-VSTR-"
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
                     END,  registration_temp_personal_information.personal_information_id)
                     END AS controlnumber'))
            ->whereRaw(('case WHEN registration_temp_personal_information.personal_information_id=1 or registration_temp_personal_information.personal_information_id=5  THEN 
            registration_temp_personal_information.status_of_transaction = "Paid" and registration_temp_personal_information.request_official_receipt="true"
             ELSE true END'))
            // ->where('registration_temp_personal_information.status_of_transaction', '=', 'Paid')
            // ->where('registration_temp_personal_information.request_official_receipt', '=', 'true')
            ->groupby('registration_temp_personal_information.personal_information_id')->distinct()
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