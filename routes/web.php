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


Route::post('/kindergarten/show/getPhotos',['uses' => 'SmallApp\Request\KindergartenGetShowPhoto@getPhotos']);


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



//园长-发布通知-user 指用户行为
Route::post("/user/leader/newNotice", ['uses' => 'SmallApp\Request\KindergartenCreateNotice@newNotice']);
//园长-创建班级类型
Route::post("/user/leader/newClassType", ['uses' => 'SmallApp\Request\KindergartenCreateClassType@newType']);
//园长-创建班级
Route::post("/user/leader/newClass", ['uses' => 'SmallApp\Request\KindergartenCreateClass@createClass']);
//得到新建教师权限页面的小程序二维码
Route::post('/user/leader/createTeacher/getQRCode',['uses' => 'SmallApp\Request\KindergartenCreateTeacher@getQRCode']);
//创建教师权限页面的小程序处理逻辑-------绑定微信账户与权限-----申请操作
Route::post('/user/leader/createTeacher/applyBind',['uses' => 'SmallApp\Request\KindergartenCreateTeacher@bindWechatApply']);
//创建教师权限页面的小程序处理逻辑-------绑定微信账户与权限-----得到待处理列表
Route::post('/user/leader/createTeacher/getWaitingList',['uses' => 'SmallApp\Request\KindergartenCreateTeacher@getWaitingList']);
//创建教师权限页面的小程序处理逻辑-------绑定微信账户与权限-----处理操作
Route::post('/user/leader/createTeacher/handle',['uses' => 'SmallApp\Request\KindergartenCreateTeacher@handle']);
//园长得到宣传页面转发次数
Route::post('/user/leader/propaganda/getVisit',['uses' => 'SmallApp\Request\KindergartenGetVisit@getData']);
//园长查询报名记录
Route::post('/user/leader/signup/select',['uses' => 'SmallApp\Request\KindergartenGetSignup@getData']);
//园长确认报名记录
Route::post('/user/leader/signup/confirm',['uses' => 'SmallApp\Request\KindergartenConfirmSignup@confirm']);
//园长得到所有本学期学生报名信息
Route::post('/user/leader/signup/selectAll',['uses' => 'SmallApp\Request\KindergartenGetSignup@getAll']);
//Route::post('/user/leader/signup/selectAll?page={page?}',['uses' => 'SmallApp\Request\KindergartenGetSignup@getAll']);


//老师发布本日家庭作业
Route::post('/user/teacher/createHomework', ['uses' => 'SmallApp\Request\TeacherCreateHomework@saveHomework']);

//公用接口
Route::post("/smallapp/common/getClassType", ['uses' => 'SmallApp\Request\CommonGetClassType@get']);
Route::post("/smallapp/common/getClass", ['uses' => 'SmallApp\Request\CommonGetClass@get']);


//幼儿园是否存在活动
Route::post("/discounts/signup/hasDiscount",['uses' => 'SmallApp\Request\DiscountsSignup@isHasDiscount']);
//幼儿园处理转发PV
Route::post("/discounts/signup/writePv",['uses' => 'SmallApp\Request\DiscountsSignup@writePv']);
//小程序优惠券部分-得到优惠转发图片
Route::post("/discounts/signup/getshowphoto",['uses' => 'SmallApp\Request\DiscountsSignup@getShowPhoto']);
//小程序优惠券部分-为某用户发放优惠券-同时处理UV
Route::post("/discounts/signup/grantDiscount",['uses' => 'SmallApp\Request\DiscountsSignup@handleGrant']);

Route::get("/composer", ['uses' => 'SmallApp\Request\DiscountsSignup@composeImg']);
//创建管理员
Route::post("/createAdmin",['uses' => 'SmallApp\Request\CreateNewAdmin@create']);

Route::post("/refresh",function (){

});



//小程序交互部分-报名
Route::group(['prefix' => '/user/signup', 'namespace' => 'SmallApp'], function () {
//    Route::get('new_archive', ['uses' => 'Request\SignUp@newArchive']);
    Route::post('signup', ['uses' => 'Request\SignUp@createSignup']);
    //用户是否有优惠券
    Route::post('hasDiscount', ['uses' => 'Request\SignUpDiscount@isHasDiscount']);
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