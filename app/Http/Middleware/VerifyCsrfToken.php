<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/user/signup/signup',
        '/user/getUserInfo',
        '/user/onLogin',
        '/user/getIdentity',
        '/user/getSession',
        '/user/center/createNewLeader/getQRCode',
        '/user/center/createNewLeader/applyBind',
        '/user/center/createNewLeader/getWaitingList',
        '/user/center/createNewLeader/handle',
        '/user/leader/newNotice',
        '/user/leader/newClassType',
        '/smallapp/common/getClassType',
        '/user/leader/newClass',
        '/smallapp/common/getClass',
        '/user/leader/createTeacher/getQRCode',
        '/user/leader/createTeacher/applyBind',
        '/user/leader/createTeacher/getWaitingList',
        '/user/leader/createTeacher/handle',
        '/user/teacher/createHomework',
        '/discounts/signup/getshowphoto',
        '/discounts/signup/grantDiscount',
        '/user/signup/hasDiscount',
        '/discounts/signup/hasDiscount',
        '/discounts/signup/writePv',
        '/user/leader/propaganda/getVisit',
        '/user/leader/signup/select',
        '/user/leader/signup/confirm',
        '/user/leader/signup/selectAll',
        '/createAdmin',
        '/kindergarten/show/getPhotos'
    ];
}
