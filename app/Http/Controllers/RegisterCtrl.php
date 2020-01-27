<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Classes\regUser;

class RegisterCtrl extends Controller
{
    public function register(Request $req) {
        $data = new regUser();
        $data->username = $req['username'];
        $data->password = Hash::make($req['password']);
        $data->fname = $req['name']['fName'];
        $data->mname = $req['name']['mName'];
        $data->lname = $req['name']['lName'];
        $data->addr = $req['address'];
        $data->cont = $req['contact'];
        try {
						$q = DB::select("CALL login('".$data->username."')");
						if(count($q) > 0) {
							return ['success' => false];
						} else {
							$qStr = "CALL add_user('".$data->username."','".$data->password."','".$data->fname."','".$data->mname."','".$data->lname."','".$data->addr."','".$data->cont."')";
	            DB::select($qStr);
						}
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false];
        }
    }
}
