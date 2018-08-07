<?php
/**
 * Created by PhpStorm.
 * User: liuxing
 * Date: 2018-08-07
 * Time: 17:23
 */

namespace App\Http\Controllers\SmallApp\Request;


use Illuminate\Support\Facades\Input;

class OnLogin
{
    public function login()
    {
        $appCode = Input::get('code');
        $appKindergarten = Input::get('kindergarten');
        dump($appKindergarten);
    }
}