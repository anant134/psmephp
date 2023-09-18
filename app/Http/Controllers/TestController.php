<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
class TestController extends BaseController
{
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login']]);
    }
    public function encryt(Request $request){
        $encryt=MD5($request->password);
        return response()->json(['type' => "success", 'data' => $encryt, 'message' => "Saved Successfully"]);
    }
    public function qrcode(Request $request){
        
        $qrcode=  QrCode::generate(1);
        
        return $qrcode;
        // response()->json(['type' => "success", 'data' => $qrcode, 'message' => "Saved Successfully"]);
    }
}
