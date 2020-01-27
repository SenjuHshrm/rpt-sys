<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes\token;
use App\Classes\alg;

class TestCtrl extends Controller
{
    public function testToken() {
        $res = new token();
        $res->username = 'TestToken';
        $res->name = 'TestToken';
        $res->iat='TestToken';
        $alg = new alg();
        $alg->alg = 'HS256';
        $alg->typ='JWT';
        $restoken = base64_encode(json_encode($alg)) . '.' . base64_encode(json_encode($res));
        $restoken = $restoken . '.' . hash_hmac('sha256', $restoken, env('JWT_SECRET'));
        return [ 'token' => $restoken ];
    }
}
