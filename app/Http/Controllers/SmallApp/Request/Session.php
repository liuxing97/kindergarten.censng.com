<?php
/**
 * Created by PhpStorm.
 * User: liuxing
 * Date: 2018-08-08
 * Time: 2:02
 */

namespace App\Http\Controllers\SmallApp\Request;



use Illuminate\Http\Request;

class Session
{
    function getSession(Request $request){

        $token = $request -> session() -> get('_token');
        return [
            'msg' => 'you can get sessionid on header',
            'time' => date('Y-m-d H:i:s')
        ];
    }
}