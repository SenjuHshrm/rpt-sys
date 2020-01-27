<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Classes\loginRes;
use App\Classes\genJWT;

class LoginCtrl extends Controller
{
    public function login(Request $request) {
        $qStr = "CALL login_web('" . $request['username'] . "')";
        $q = DB::select($qStr);
        $resobj = new loginRes();
        $jwtgen = new genJWT();
        if(count($q) == 1){
            $data = $q[0];
            if(Hash::check($request['password'], $data->password)) {
								if($data->middle_name == null) {
									$name = $data->first_name . ' ' . $data->last_name;
								} else {
									$name = $data->first_name . ' ' . substr($data->middle_name,0,1) . '. ' . $data->last_name;
								}
                $resobj = new loginRes();
                $jwtgen = new genJWT();
                $resobj->success = true;
                $resobj->status = 'Valid credentials';
                $resobj->token = $jwtgen->generate($data->username, $name, $data->account_type);
                return json_encode($resobj);
            } else {
                $resobj->success = false;
                $resobj->status = 'Wrong Password';
                $resobj->token = null;
                return json_encode($resobj);
            }
        } else {
            $resobj->success = false;
            $resobj->status = 'Username not registered';
            $resobj->token = null;
            return json_encode($resobj);
        }
    }
}
