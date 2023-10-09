<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->group(['prefix' => 'api', 'middleware' => 'auth'], function () use ($router) {
    $router->post('logout','UserController@logout');
    $router->group(['prefix'=>'slot'],function($router){
        $router->get('getslots','SlotController@show');
        $router->get('getallslots','SlotController@getAllSlots');
        $router->post('saveslot','SlotController@saveSlot');
    });
    $router->group(['prefix'=>'appointment'],function($router){
        $router->post('interview','AppointmentController@saveAppointment');  
    });
    $router->group(['prefix'=>'user'],function($router){
        $router->get('getUser','UserController@getuserinfor');
        $router->get('getAllUser','UserController@getAlluser');
        $router->post('updateuser','UserController@updateuser');
        $router->post('saveuser','UserController@addUpdateUser');
    });
    //transaction
    $router->group(['prefix'=>'transaction'],function($router){
        $router->get('getTransaction','TransactionController@getTransaction');
        $router->post('updateStatus','TransactionController@updateStatus');
    });

    //static page
    $router->group(['prefix'=>'staticpage'],function($router){
        $router->get('getStaticPage','StaticDataController@getStaticPage');
        $router->post('saveStaticPage','StaticDataController@saveStaticPage');
    });

     //acount
     $router->group(['prefix'=>'account'],function($router){
        $router->get('getAccount','AccountController@getAccount');
        $router->post('addUpdateAccount','AccountController@addUpdateAccount');
    });
     //Role
     $router->group(['prefix'=>'role'],function($router){
        $router->get('getRole','RoleController@getRole');
        $router->get('getRoleoption','RoleController@getRoleoption');
        $router->get('getRoleByID','RoleController@getRoleByID');
        $router->post('addUpdateRole','RoleController@addUpdateRole');
    });
     //Routes
     $router->group(['prefix'=>'routes'],function($router){
        $router->get('getRoutes','RoutesController@getRoutes');
        $router->get('getRoutesByID','RoutesController@getRoutesByID');
        $router->post('addUpdateRoutes','RoutesController@addUpdateRoutes');
    });
    $router->group(['prefix'=>'app'],function($router){
        $router->post('claim','AppController@claim');
    });
    $router->group(['prefix'=>'member'],function($router){
        $router->get('getAllMember','MemberController@getAllMember');
        $router->get('getMemberwithlimit','MemberController@getMemberwithlimit');
        $router->get('getRegistrationType','MemberController@getRegistrationType');
        $router->get('getMemberType','MemberController@getMemberType');
        $router->post('saveMemberType','MemberController@saveMemberType');
        $router->get('removeDuplicate','MemberController@removeDuplicate');
        $router->post('deletemember','MemberController@deletemember');
        $router->post('updateMember','MemberController@updateMember');
        
        $router->get('getScanmember','MemberController@getScanmember');
        $router->get('getIndustry','MemberController@getIndustry');
        $router->get('getChapter','MemberController@getChapter');
        
    });
});
$router->group(['midlleware'=>'auth','prefix'=>'api'],function($router){
    
});
$router->group(['prefix'=>'test'],function($router){
    $router->post('encryt','TestController@encryt');
    $router->post('testjwt','UserController@postLogin');
    $router->post('qrcode','TestController@qrcode');
});

$router->group(['prefix'=>'api'],function($router){
    $router->post('login','UserController@postLogin');
    $router->get('getAppSetting','AppSettingController@getAppSetting');
    $router->get('all','AccountController@show');
    $router->get('usertype','UsertypeController@getAllUserType');
    $router->get('getCategory','CategoryController@show');
    $router->get('getSlot','SlotController@show');
    $router->get('province','ProvinceController@show');
    $router->get('citybyprovince','ProvinceController@getCity');
    $router->post('digest','UsertypeController@digest');

    //country
    $router->get('getCountry','CountryController@getCountry');
    //Province by country id 
    $router->get('getProvinceByCountryId','ProvinceController@getProvinceByCountryId');
    //City by province id 
    $router->get('getcitybyprovinceId','ProvinceController@getCityByProvinceId');
    //fee
    $router->get('getFee','FeeController@getFee');
    //save file
    $router->post('savefile','FileUploadController@saveFile');
    //get file
    $router->get('getfile','FileUploadController@getFileUrl');
    //common
    $router->get('commonMaste','CommonController@getCommonMaster');

    $router->group(['prefix'=>'user'],function($router){
        $router->post('registration','UserController@userregistration');
        $router->post('updateuser','UserController@updateuser');
    });
    $router->get('postback','PaymentController@postback');
    //payment
    $router->group(['prefix'=>'payment'],function($router){
        $router->post('pay','PaymentController@pay');
        $router->post('generate','PaymentController@generate');
    });

    //member
    
    $router->post('searchmember','MemberController@SearchMember');
    $router->post('saveMember','MemberController@saveMember');
    $router->get('getChartData','MemberController@getChartData');
    $router->get('checkPaystatusandupadte','PaymentController@checkPaystatusandupadte');
    $router->get('updateMemberControlnumber','MemberController@updateMemberControlnumber');
    $router->group(['prefix'=>'member'],function($router){
        $router->post('bulkuploadMember','MemberController@saveBulkUpload');
    });
   
    
    
});


