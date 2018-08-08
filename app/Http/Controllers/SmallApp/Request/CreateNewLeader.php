<?php

namespace App\Http\Controllers\SmallApp\Request;

use App\Http\Controllers\SmallApp\Common\AccessToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CreateNewLeader extends Controller
{
    //创建园长账户
    function create()
    {
        //得到access_token
        $ACCESS_TOKEN = new AccessToken();
        $ACCESS_TOKEN = $ACCESS_TOKEN -> get();
        return $ACCESS_TOKEN;
    }
}
