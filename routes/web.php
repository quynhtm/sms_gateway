<?php
Auth::routes();

const Admin = "Admin";

const Api = "Api";

//namnv api get token
Route::post('oauth2/token', array('as' => 'oauth2.token','uses' => Api.'\ApiGetToken@getToken'));
Route::get('oauth2/token', function () {
    return response(json_encode(array("ip"=>$_SERVER['REMOTE_ADDR'],"hello"=>"Welcome to SMSGateways Service")), 200)
        ->header('Content-Type', 'application/json');
});
Route::post('api/push-sms', array('as' => 'api.pushSms','uses' => Api.'\ApiPushSms@authorization'));
Route::post('api/push-list-sms', array('as' => 'api.pushListSms','uses' => Api.'\ApiPushSms@authorization'));
Route::get('api/push-sms', function () {
    return response(json_encode(array("status_code"=>\App\Library\AdminFunction\Define::HTTP_STATUS_CODE_405,"message"=>"Method Not Allowed")), 200)
        ->header('Content-Type', 'application/json');
});

// Used for dev by Quynh
$isDev = Request::get('is_debug','');
if($isDev == 'tech_code'){
    Session::put('is_debug_of_tech', '13031984');
    Config::set('compile.debug',true);
}
if(Session::has('is_debug_of_tech')){
    Config::set('compile.debug',true);
}

//Quan tri CMS cho admin
Route::get('quan-tri.html', array('as' => 'admin.login','uses' => Admin.'\AdminLoginController@loginInfo'));
Route::post('quan-tri.html', array('as' => 'admin.login','uses' => Admin.'\AdminLoginController@login'));

Route::group(array('prefix' => 'manager', 'before' => ''), function(){
	require __DIR__.'/admin.php';
});

//Router Site
Route::group(array('prefix' => 'api', 'before' => ''), function () {
    require __DIR__.'/api.php';
});