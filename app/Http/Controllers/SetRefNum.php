<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CheckRequestAuth;

class SetRefNum extends Controller
{
    public function set(Request $req) {
			$refnum = '';
			$header = $req->header('Authorization');
			$test = new CheckRequestAuth();
			if($test->testToken($header)) {
				$res = DB::select("CALL new_ref_entry('".$req['type']."', '".$req['reqIn']."', '".$req['reqId']."')");
				$id = $res[0]->id;
				$idLn = strlen($id);
				$TRNum = 7 - $idLn;
				if($idLn <= 7) {
					$pre = '';
					for($x = 0; $x < $TRNum; $x++) {
						$pre = $pre . '0';
					}
					$refnum = $pre . $id;
				} else {
					$refnum = substr(strval($id), -7);
				}
				DB::select("CALL set_ref('".$refnum."', ".$id.")");
				return json_encode([ 'ref' => $refnum ]);
			} else {
				return json_encode([ 'ref' => false ]);
			}
		}
}
