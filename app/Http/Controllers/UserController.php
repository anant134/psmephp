<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTAuth;
class UserController extends Controller
{
    protected $jwt;
    public function __construct()
    {   
 //       $this->middleware('auth:api', ['except' => ['postLogin']]);
    }
    public function me()
    {
        return response()->json(auth()->user());
    }
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    public function login(Request $request){
        try {
            if(empty($request->username) || empty($request->username)){
                return ['resultKey' => 0, 'resultValue' => 
                    null, 'errorCode' => 'err1', 'defaultError' => 'Credentials are required'];
            }
            $request->password=md5($request->password);
            $userexist = User::where(['username' => $request->username])
                             ->where(['password' => $request->password])->first();
                             if($userexist){
                                return response()->json(["resultValue" => $userexist, 'resultKey' => 1, 'defaultError' => null, 'resultResponse' => 'error'], 200);
     
                             }else {
                                return ['resultKey' => 0, 'resultValue' => 
                    null, 'errorCode' => 'err1', 'defaultError' => 'Invaild username/password'];
                             }
    
        } catch (\Exception $e) {
            return response()->json(["resultValue" => $e->getMessage(), 'resultKey' => 0, 'defaultError' => null, 'resultResponse' => 'error'], 200);
       
        }
        
    }
    public function userregistration(Request $request){
        try {
            DB::beginTransaction();
            $this->validate($request, [
                'account_id' => 'required',
                'emailid' => 'required',
                'password' => 'required',
                'firstname' => 'required',
                'phonenumber' => 'required'
            ]);
            

            $userexist=User::where(['emailid' => $request->emailid])->count();
            if($userexist!=0){
                return ['resultKey' => 0, 'resultValue' => 
                null, 'errorCode' => 'err1', 'defaultError' => 'User already exist.'];
            }
            $encrytpass=Hash::make($request->password);
           
            $user = User::create([
                            'account_id'=>$request->get("account_id", null), 
                            'usertype_id'=>3,
                            'role_id'=>1,
                            'firstname' => $request->get("firstname", null), 
                            'lastname' => $request->get("lastname", null), 
                            'middlename' =>$request->get("middlename", null), 
                            'emailid' => $request->get("emailid", null),
                            'phonenumber'=> $request->get("phonenumber", null),
                            'password' => $encrytpass,
                            'is_active' => 1, 
                            'birthdate'=> $request->get("birthdate", null),
                            'gender_id'=> $request->get("gender_id", null),
                            'address1'=> $request->get("address1", null), 
                            'address2'=> $request->get("address2", null), 
                            'address3'=> $request->get("address3", null),
                            'zipcode'=> $request->get("zipcode", null),  
                            'country_id'=> is_null($request->get("country_id", null))?null:$request->get("country_id", null)["id"], 
                            'province_id'=>is_null($request->get("province_id", null))?null:$request->get("province_id", null)["id"], 
                            'city'=> is_null($request->get("city", null))?null:$request->get("city", null)["id"],    
                        ]);

                        $usetoken=$this->postLogin(new Request(
                            ["emailid" =>$request->get("emailid", null),
                                "password" => "1234",
                        ]));

                       $result= ["user"=>$user,"token"=>$usetoken->getData("content")["access_token"]];

                        DB::commit();
                        return response()->json(["resultValue" => $result, 'resultKey' => 1, 'defaultError' => null, 'resultResponse' => ''], 200);
     
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["resultValue" => "", 'resultKey' => 0, 'defaultError' => $e->getMessage(), 'resultResponse' => ''], 200);
       
        }
        
    }
    public function updateuser(Request $request){
        
        try {
            DB::beginTransaction();
           
            $active= $request->has("active")?$request->active:null;
            $obj = User::find($request->id);
            if($obj){
                
                $obj->active=$active==null?0:$active;
               // $obj->category=$request->has("category")?$request->category:null;
                $obj->firstname=$request->has("firstname")?$request->firstname:null;
                $obj->middlename=$request->has("middlename")?$request->middlename:null;
                $obj->lastname=$request->has("lastname")?$request->lastname:null;
                $obj->rank=$request->has("rank")?$request->rank:null;
                $obj->afp_serial_no=$request->has("afpsn")?$request->afpsn:null;
                $obj->branch_of_service=$request->has("brsv")?$request->brsv:null;
                $obj->retirement_date=$request->has("retdate")?$request->retdate:null;
                $obj->vfp_organization=$request->has("vfp_organization")?$request->vfp_organization:null;
                $obj->address=$request->has("address")?$request->address:null;
                $obj->zipcode=$request->has("zipcode")?$request->zipcode:null;
                $obj->birthdate=$request->has("dob")?$request->dob:null;
                $obj->place_of_birth=$request->has("placeofbirth")?$request->placeofbirth:null;
                $obj->gender=$request->has("gender")?$request->gender:null;
                $obj->civil_status=$request->has("civilstatus")?$request->civilstatus:null;
                $obj->blood_type=$request->has("bloodtype")?$request->bloodtype:null;
                $obj->height=$request->has("height")?$request->height:null;
                $obj->weight=$request->has("weight")?$request->weight:null;
                $obj->philhealth_no=$request->has("philhealth_no")?$request->philhealth_no:null;
                $obj->national_id_no=$request->has("national_id_no")?$request->national_id_no:null;
                $obj->tin_no=$request->has("tin_no")?$request->tin_no:null;
                $obj->religion=$request->has("religion")?$request->religion:null;
                $obj->spouse_name=$request->has("spousename")?$request->spousename:null;
                $obj->date_of_death_if_widow=$request->has("date_of_death")?$request->date_of_death:null;
                $obj->father_name=$request->has("father_name")?$request->father_name:null;
                $obj->maiden_name_of_mother=$request->has("maiden_name_of_mother")?$request->maiden_name_of_mother:null;
                $obj->name_of_contact_person=$request->has("contact_person_name")?$request->contact_person_name:null;
                $obj->address_of_contact_person=$request->has("contact_person_address")?$request->contact_person_address:null;
                $obj->mobile_number_contact_person=$request->has("contact_person_mobile_number")?$request->contact_person_mobile_number:null;
                $obj->phone=$request->has("phone")?$request->phone:null;
                $obj->province=$request->has("province")?$request->province:null;
                $obj->city=$request->has("city")?$request->city:null;
                $obj->isliving=$request->has("isliving")?$request->isliving:null;
                $obj->isUpdateFirst=$request->has("isupdatefirst")?$request->isupdatefirst:null;
                $obj->update();
              
              
              //  dependent: this.dependent,
              
              
              
                DB::commit();
                return response()->json(["resultValue" => $obj, 'resultKey' => 1, 'defaultError' => null, 'resultResponse' => 'error'], 200);

            }else{
                DB::rollBack();
                return response()->json(["resultValue" => $obj, 'resultKey' => 0, 'defaultError' => 'User not found for the requested id', 'resultResponse' => 'error'], 200);

            }
            
           
          
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["resultValue" => $e->getMessage(), 'resultKey' => 0, 'defaultError' => null, 'resultResponse' => 'error'], 200);
       
        }




    }
    public function getuserinfor(Request $request){
        try {
            $user= auth()->user();
            return response()->json(["resultValue" =>  $user, 'resultKey' => 1, 'defaultError' => null, 'resultResponse' => ''], 200);

        } catch (\Exception $e) {
            return response()->json(["resultValue" => $e->getMessage(), 'resultKey' => 0, 'defaultError' => null, 'resultResponse' => 'error'], 200);
        
        }
    }
    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);
           
        try {

            $credentials = $request->only(['username', 'password']);
            if (! $token = Auth::attempt($credentials)) {
                
                return response()->json([ "resultKey"=>1,
                                        "resultValue"=>[],
                                        "errorCode" => 601,
                                        "errorMsg" => "Invalid credentials"
                    ], 200);
            }

            $userinfor=auth()->user();
            $userr= User::where('id',$userinfor->id)->with('role')->get();
            return $this->respondWithTokenWithUser($token,$userr);

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json([ "resultKey"=>1,
            "resultValue"=>[],
            "errorCode" => 602,
            "errorMsg" => "Token Expired"
            ], 200);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            //return response()->json(['token_invalid'], 500);
            return response()->json([ "resultKey"=>1,
            "resultValue"=>[],
            "errorCode" => 603,
            "errorMsg" => "Token Invalid"
            ], 200);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            // return response()->json(['token_absent' => $e->getMessage()], 500);
            return response()->json([ "resultKey"=>1,
            "resultValue"=>[],
            "errorCode" => 604,
            "errorMsg" => $e->getMessage()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([ "resultKey"=>1,
            "resultValue"=>[],
            "errorCode" => 604,
            "errorMsg" => $e->getMessage()
            ], 604);
    
        }

        return response()->json('error');
    }
    public function test()
    {

        $token = $this->jwt->getToken();
        $this->jwt->user();
        $data = $this->jwt->setToken($token)->toUser();
        print_r($data);
        // echo "inside controller";

    }
    protected function respondWithToken($token)
    {
        return response()->json([  
            'success' => true, 
            'access_token' => $token,
            'token_type' => 'bearer',
        ]);
    }
    protected function respondWithTokenWithUser($token,$user)
    {
            $user->access_token=$token;
       // ['resultKey' => 1, 'resultValue' => $data, 'errorCode' => null,'errorMsg' => null], 200
        return response()->json([  
            "resultKey"=>1,
            "resultValue"=>[
            'access_token'=>$token,
            'userinfo'=>$user]
        ]);
    }
    
   
}
