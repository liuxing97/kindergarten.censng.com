<?php

namespace App\Http\Controllers\SmallApp\Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SignUpResponse extends Controller
{
    public function signUpfail()
    {
        echo "signup has fail";
        return false;
    }
    public function signUpSeccess()
    {
        echo "signup has seccess";
        return true;
    }
}
