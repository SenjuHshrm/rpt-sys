<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CheckRequestAuth;

class GetBldgAddItems extends Controller
{
    public function getItems(Request $req) {
      $header = $req->header('Authorization');
      $test = new CheckRequestAuth();
      if($test->testToken($header)) {
        $res = DB::select("CALL get_building_increment_to_unit_market_values()");
        return json_encode($res);
      } else {
        return json_encode([]);
      }
    }
}
