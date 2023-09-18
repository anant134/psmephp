<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Fee;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Http\Classes\AWS\S3Bucket;
use Laravel\Lumen\Routing\Controller as BaseController;
use Validator;
class FileUploadController extends BaseController
{
    public function saveFile(Request $request){
        try {
            $data= "";
            $this->validate($request, [
                'image' => 'required'
            ]);
            $base64Image = explode(";base64,", $request->image);
            $explodeImage = explode("image/", $base64Image[0]);
            $imageName = $explodeImage[1];
            $image_base64 = base64_decode($base64Image[1]);
              $time = time(); 
            $temppath = 'customizecard/'.$request->get("user_id", null).'_' . $time . "." .  $imageName;
            $path=$temppath;
            $path = Storage::disk('s3')->put($path,$image_base64); 
            if($path){
                
                DB::beginTransaction();
                $this->validate($request, [
                    'account_id' => 'required',
                    'user_id' => 'required'
                ]);
                $to_insert = [
                    "user_id" =>$request->get("user_id", null),
                    "account_id" =>$request->get("account_id", null),
                    "cardimagepath" =>$temppath,
                ];
              //  SeriesMaster::updateOrCreate(["id" => null], $to_insert);
         
                $transaction= Transaction::updateOrCreate(["id" => null], $to_insert);
                $resultTrans[]=$transaction;
                // $s3Image = S3Bucket::GetFromBucket(env('AWS_ACCESS_KEY_ID'), env('AWS_SECRET_ACCESS_KEY'), env('AWS_DEFAULT_REGION'),
                //            env('AWS_BUCKET'),  $temppath , null, true,60);
                // $result=["img"=>$s3Image];
                DB::commit();
                return response()->json(['resultKey' => 1, 'resultValue' => $resultTrans, 'errorCode' => null,'errorMsg' => null], 200);
            }else{
                DB::rollBack();
                return response()->json(['resultKey' => 0, 'resultValue' => "Error while saving in s3", 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
      
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
        }
    }
    public function getFileUrl(Request $request){
        try {
            $this->validate($request, [
                'imagename' => 'required'
            ]);
            $s3Image = S3Bucket::GetFromBucket(env('AWS_ACCESS_KEY_ID'), env('AWS_SECRET_ACCESS_KEY'), env('AWS_DEFAULT_REGION'),
                            env('AWS_BUCKET'),  $request->get("imagename", null) , null, true,60);
                    $result=["img"=>$s3Image];

                    return response()->json(['resultKey' => 1, 'resultValue' => $result, 'errorCode' => null,'errorMsg' => null], 200);
                   
        } catch (\Exception $ex) {
            return response()->json(['resultKey' => 0, 'resultValue' => null, 'errorCode' => 1,'errorMsg' => $ex->getMessage()], 200);
    
        }
    }
}