<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CheckRequestAuth;

class BldgValuesCtrl extends Controller
{

		private function checkAuth($header) {
			$test = new CheckRequestAuth();
			if($test->testToken($header)) {
				return true;
			} else {
				return false;
			}
		}

    public function getBldgKind(Request $req) {
			if($this->checkAuth($req->header('Authorization'))) {
				$q = DB::select("CALL get_building_unit_market_values()");
				$resp = array();
				foreach($q as $qry) {
					array_push($resp, $qry);
				}
				return json_encode([ 'res' => $resp ]);
			} else {
				return json_encode([ 'res' => false ]);
			}
		}
}
