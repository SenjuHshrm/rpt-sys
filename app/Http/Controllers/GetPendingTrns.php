<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CheckRequestAuth;

class GetPendingTrns extends Controller
{
    public function getPendingSegregation(Request $req, $name) {
			$header = $req->header('Authorization');
			$test = new CheckRequestAuth();
			if($test->testToken($header)) {
        $id = $this->getEncoderId($name);
				return json_encode(DB::select("CALL get_pending_segregated_lands('".$id."')"));
			} else {
				return json_encode([ 'res' => false ]);
			}
		}

		public function getPendingConsolidation(Request $req, $name) {
			$header = $req->header('Authorization');
			$test = new CheckRequestAuth();
			if($test->testToken($header)) {
        $id = $this->getEncoderId($name);
				return json_encode(DB::select("CALL get_pending_consolidated_lands('".$id."')"));
			} else {
				return json_encode([ 'res' => false ]);
			}
		}

		public function getPendingSubdivision(Request $req, $name) {
			$header = $req->header('Authorization');
			$test = new CheckRequestAuth();
			if($test->testToken($header)) {
        $id = $this->getEncoderId($name);
				return json_encode(DB::select("CALL get_pending_subdivided_lands('".$id."')"));
			} else {
				return json_encode([ 'res' => false ]);
			}
		}

    private function getEncoderId($name) {
      $res = DB::select("CALL get_encoder_id('".$name."')");
      return $res[0]->user_id;
    }
}
