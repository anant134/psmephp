<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    //
    protected function responseWithToken($token){
        return response()->json(
            [
                'token'=>$token,
                'token_type'=>'bearer',
                'expire_in'=>Auth::factory()->getTTL() * 60
                
            ],200
        );
    }
}
