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
        '/signup/new_archive',
        '/user/getUserInfo',
        '/user/onLogin',
        '/user/getIdentity',
        '/user/getSession',
        '/user/center/createNewLeader/getQRCode',
        '/user/center/createNewLeader/applyBind',
        '/user/center/createNewLeader/handle',
        '/user/center/createNewLeader/getWaitingList',
        '/user/leader/newNotice',
        '/user/leader/newClassType',
        '/smallapp/common/getClassType'
    ];
}
