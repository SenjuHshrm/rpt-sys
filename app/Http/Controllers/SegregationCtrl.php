<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CheckRequestAuth;

class SegregationCtrl extends Controller
{
    public function searchRecord($sysCaller, $searchBy, $info, Request $req) {
        $header = $req->header('Authorization');
				$test = new CheckRequestAuth();
				if($test->testToken($header)) {
					$res = DB::select("CALL search_land_faas('".$info."', '".$searchBy."', '".$sysCaller."')");
          return [
              'success' => true ,
              'err' => null,
              'data' => $res
          ];
				} else {
					return [
	            'success' => false ,
	            'err' => 'Invalid Token',
	            'data' => null
	        ];
				}
    }
}
