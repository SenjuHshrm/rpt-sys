<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CheckRequestAuth;

class GetMarketValues extends Controller
{
    public function getVal(Request $req) {
			$header = $req->header('Authorization');
			$test = new CheckRequestAuth();
			if($test->testToken($header)) {
				$q = DB::select("CALL get_land_unit_market_values()");
				return json_encode($q);
			} else {
				return json_encode([ 'res' => false ]);
			}
		}
}
