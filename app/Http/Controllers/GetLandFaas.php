<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CheckRequestAuth;

class GetLandFaas extends Controller
{
    public function getInfo(Request $request, $id) {
				$test = new CheckRequestAuth();
				$header = $request->header('Authorization');
				if($test->testToken($header)) {
					$res = DB::select("CALL get_land_faas(".$id.")");
	        $obj = $res[0];
	        $obj->encoder_id = $this->getEncoder($obj->encoder_id);
	        return json_encode([
						'faas' => $obj,
						'owners' => DB::select("CALL get_land_faas_owners(".$id.")"),
						'admins' => DB::select("CALL get_land_faas_administrators(".$id.")"),
						'strips' => DB::select("CALL get_land_faas_strips(".$id.")"),
						'marketval' => DB::select("CALL get_land_faas_adjustment_factors(".$id.")")
					]);
				} else {
					return json_encode([]);
				}
    }

    private function getEncoder($id) {
        $q = DB::select("CALL get_user_credentials_web(".$id.")");
        if(count($q) != 0) {
          if($q[0]->middle_name == '') {
              $res = $q[0]->first_name . ' ' .$q[0]->last_name;
          } else {
              $res = $q[0]->first_name . ' ' . substr($q[0]->middle_name, 0, 1) . '. ' .$q[0]->last_name;
          }
        }
        return $res;
    }
}
