<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CheckRequestAuth;

class PosHolders extends Controller
{
    public function getHolders(Request $req, $sysCaller) {
				$header = $req->header('Authorization');
				$test = new CheckRequestAuth();
				if($test->testToken($header)) {
					$q = DB::select("CALL get_position_holder_links('".$sysCaller."')");
	        return json_encode($q);
				} else {
					return json_encode([]);
				}
    }
}
