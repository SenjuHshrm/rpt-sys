<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CheckRequestAuth;

class GetBldgFaas extends Controller
{
    public function getInfo(Request $req, $id) {
				$header = $req->header('Authorization');
				$test = new CheckRequestAuth();
				if($test->testToken($header)) {
					$res = DB::select("CALL get_building_faas(".$id.")");
	        $obj = $res[0];
	        $obj->encoder_id = $this->getEncoder($obj->encoder_id);
	        return json_encode([
            'faas' => $obj,
            'owners' => DB::select("CALL get_building_faas_owners(".$id.")"),
            'admins' => DB::select("CALL get_building_faas_administrators(".$id.")"),
          ]);
				} else {
					return json_encode([]);
				}
    }

    private function getEncoder($id) {
        $q = DB::select("CALL get_user_credentials(".$id.")");
        if($q[0]->middle_name == '') {
            $res = $q[0]->first_name . ' ' .$q[0]->last_name;
        } else {
            $res = $q[0]->first_name . ' ' . substr($q[0]->middle_name, 0, 1) . '. ' .$q[0]->last_name;
        }
        return $res;
    }
}
