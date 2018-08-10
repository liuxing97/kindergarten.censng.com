<?php

Route::get('/', function () { return redirect('/admin/home'); });

// Authentication Routes...
$this->get('login', 'Auth\LoginController@showLoginForm')->name('auth.login');
$this->post('login', 'Auth\LoginController@login')->name('auth.login');
$this->post('logout', 'Auth\LoginController@logout')->name('auth.logout');

// Change Password Routes...
$this->get('change_password', 'Auth\ChangePasswordController@showChangePasswordForm')->name('auth.change_password');
$this->patch('change_password', 'Auth\ChangePasswordController@changePassword')->name('auth.change_password');

// Password Reset Routes...
$this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('auth.password.reset');
$this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('auth.password.reset');
$this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
$this->post('password/reset', 'Auth\ResetPasswordController@reset')->name('auth.password.reset');

Route::group(['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/home', 'HomeController@index');
    Route::resource('permissions', 'Admin\PermissionsController');
    Route::post('permissions_mass_destroy', ['uses' => 'Admin\PermissionsController@massDestroy', 'as' => 'permissions.mass_destroy']);
    Route::resource('roles', 'Admin\RolesController');
    Route::post('roles_mass_destroy', ['uses' => 'Admin\RolesController@massDestroy', 'as' => 'roles.mass_destroy']);
    Route::resource('users', 'Admin\UsersController');
    Route::post('users_mass_destroy', ['uses' => 'Admin\UsersController@massDestroy', 'as' => 'users.mass_destroy']);
});


//小程序所有用户的初始动作
Route::post('/user/getSession',['uses' => 'SmallApp\Request\Session@getSession']);
Route::get('/user/getUserInfo', ['uses' => 'SmallApp\Request\GetUserInfo@getIdentity']);
Route::post('/user/onLogin', ['uses' => 'SmallApp\Request\OnLogin@login']);
Route::post('/user/getIdentity',['uses' => 'SmallApp\Request\GetUserInfo@getIdentity']);

//得到新建园长权限页面的小程序二维码
Route::post('/user/center/createNewLeader/getQRCode',['uses' => 'SmallApp\Request\CreateNewLeader@getQRCode']);
//新建园长权限页面的小程序处理逻辑-------绑定幼儿园小程序与微信账户-----申请操作
Route::post('/user/center/createNewLeader/applyBind',['uses' => 'SmallApp\Request\CreateNewLeader@bindWechatApply']);
//新建园长权限页面的小程序处理逻辑-------绑定幼儿园小程序与微信账户-----得到待处理列表
Route::post('/user/center/createNewLeader/getWaitingList',['uses' => 'SmallApp\Request\CreateNewLeader@getWaitingList']);
//新建园长权限页面的小程序处理逻辑-------绑定幼儿园小程序与微信账户-----处理操作
Route::post('/user/center/createNewLeader/handle',['uses' => 'SmallApp\Request\CreateNewLeader@handle']);



//园长-发布通知
Route::post("/user/leader/newNotice",['uses' => 'SmallApp\Request\KindergartenNewNotice@newNotice']);





//小程序交互部分-报名
Route::group(['prefix' => 'signup', 'namespace' => 'SmallApp'], function () {
//    Route::get('new_archive', ['uses' => 'Request\SignUp@newArchive']);
    Route::post('new_archive', function ()
    {

    });
});

//小程序交互部分-相册
Route::group(['prefix' => 'signup'], function () {

});

//小程序交互部分-资料
Route::group(['prefix' => 'signup'], function () {

});

//小程序交互部分-儿歌
Route::group(['prefix' => 'signup'], function () {

});

//小程序交互部分-故事
Route::group(['prefix' => 'signup'], function () {
//    wx22fb4ce531885f1e
//    316369f79f2a76476abc50cf52cd6303
});

//小程序交互部分-识字
Route::group(['prefix' => 'signup'], function () {

});