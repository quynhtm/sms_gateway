<?php
Auth::routes();

const Admin = "Admin";
const Api = "Api";


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

//namnv api get token
Route::get('oauth2/token', array('as' => 'oauth2.token','uses' => Api.'\ApiGetToken@welcome'));
Route::post('oauth2/token', array('as' => 'oauth2.token','uses' => Api.'\ApiGetToken@getToken'));