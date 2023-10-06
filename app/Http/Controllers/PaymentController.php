<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Setting;
use App\Models\EventRegistartion;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Validator;
class PaymentController extends BaseController
{
    // anant

    public function pay(Request $request){
        $data=$request->all();
        try {
            $this->validate($request, [
                'amount' => 'required',
                'customer_email' => 'required',
                'customer_name' => 'required',
            ]);
            $setting=Setting::first();
            if($setting){
                $merchant_order_no = random_int(100000, 999999);
            
                $rawdata =array(
                    "merchant_order_no" => $merchant_order_no,
                    "amount" => number_format((float)$request->get("amount", null), 2, '.', ''),
                    "currency" => "PHP",
                    "customer_email" => $request->get("customer_email", null),
                    "customer_name" => $request->get("customer_name", null),
                    "description" => "Beep Card",
                   
                );
                $digest=$this->_generate_digest($rawdata,$setting->payment_secretkey);
                
                $processdata =array(
                    "merchant_order_no" => $merchant_order_no,
                    "amount" => number_format((float)$request->get("amount", null), 2, '.', ''),
                    "currency" => "PHP",
                    "customer_email" => $request->get("customer_email", null),
                    "customer_name" => $request->get("customer_name", null),
                    "description" => "Beep Card",
                    'digest' => $digest
                );
                
                
                // URL
                $apiURL =env('paymentURl').'order' ;
                // POST Data
                $postInput = $processdata;
                //FTuMC2JHoNkaU8sR
                $headers = [
                    'Authorization' => 'Bearer '.$setting->payment_token
                ];
                $response = Http::withHeaders($headers)->post($apiURL, $postInput);
                $statusCode = $response->status();
                $responseBody = json_decode($response->getBody(), true);
                if($responseBody["status"]){
                    return response()->json(['resultKey' => 1, 'resultValue' => $responseBody, 'errorCode' => null,'errorMsg' => null], 200);
                }else{
                    return response()->json(['resultKey' => 0, 'resultValue' => $responseBody, 'errorCode' => null,'errorMsg' => null], 200);
            
                }
            }else {
                return response()->json(['resultKey' => 0, 'resultValue' => "", 'errorCode' => null,'errorMsg' => "No setting is defined"], 200);
            
            }
            
           
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
       
    }
    public function generate(Request $request){
        $setting=Setting::first();
        $merchant_order_no =198024;
        // random_int(100000, 999999);
        $rawdata =array(
            "merchant_order_no" => $merchant_order_no,
            "amount" => number_format((float)$request->get("amount", null), 2, '.', ''),
            "currency" => "PHP",
            "customer_email" => $request->get("customer_email", null),
            "customer_name" => $request->get("customer_name", null),
            "description" => "Beep Card",
        );
        $digest=$this->_generate_digest($rawdata,$setting->payment_secretkey);
        $processdata =array(
            "merchant_order_no" => $merchant_order_no,
            "amount" => number_format((float)$request->get("amount", null), 2, '.', ''),
            "currency" => "PHP",
            "customer_email" => $request->get("customer_email", null),
            "customer_name" => $request->get("customer_name", null),
            "description" => "Beep Card",
            'digest' => $digest,
            'secretkey'=>$setting->payment_secretkey
        );
        return response()->json(['resultKey' => 1, 'resultValue' => $processdata, 'errorCode' => null,'errorMsg' => null], 200);
              
    }
    public function _generate_digest($params, $secret_key)
    {
        ksort($params); 
        $data_string = ''; 
        foreach ($params as $key => $value) { $data_string .= $value . '|'; } 
        return sha1($data_string . $secret_key);
    }


    public function checkPaystatusandupadte(Request $request){

                $event=EventRegistartion::where('status_of_transaction',1)
                ->where('reference_code',"<>","") ;
                $event=$event->get();
                for ($i=0; $i < count($event); $i++) {
                    $apiURL ="https://api.smartpay.net.ph/order?reference_number=".$event[$i]->reference_code ;
                    $headers = [
                        'Authorization' => 'Bearer zaRCpNqgwvGkP2QH'
                    ];
                    $response = Http::withHeaders($headers)->get($apiURL);
                    $statusCode = $response->status();
                    $responseBody = json_decode($response->getBody(), true);
                    $s="";
                    $printdata=["code"=>$event[$i]->reference_code,"status"=>$responseBody["results"]["data"]["status"]];
                    print_r($printdata);
                    $updatevent=EventRegistartion::find($event[$i]->id);
                    if($responseBody["results"]["data"]["status"]!="success"){
                        $updatevent->status_of_transaction=0;
                        $updatevent->save();
                    }else{
                        $updatevent->controlnum=null;
                        $updatevent->save();
                    }
                    
                }


                
                return response()->json(['resultKey' => 1, 'resultValue' => 1, 'errorCode' => null,'errorMsg' => null], 200);
      

    }




}
