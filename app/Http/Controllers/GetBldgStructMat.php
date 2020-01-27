<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CheckRequestAuth;

class GetBldgStructMat extends Controller
{
    public function getLs(Request $req) {
      $header = $req->header('Authorization');
      $test = new CheckRequestAuth();
      if($test->testToken($header)) {
        $q = DB::select("CALL get_building_structure_materials()");
        return json_encode($q);
      } else {
        return json_encode([ 'res' => false ]);
      }
    }
}
