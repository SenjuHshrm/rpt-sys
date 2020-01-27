<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\CheckRequestAuth;
use Illuminate\Support\Facades\DB;


class GetAllBldgDefVal extends Controller
{
  public function getVals(Request $req) {
    if($this->checkAuth($req->header('Authorization'))) {
      return json_encode([
        'success' => true,
        'res' => [
          'bldgInc' => DB::select("CALL get_building_increment_to_unit_market_values()"),
          'bldgRate' => DB::select("CALL get_building_ratings()"),
          'bldgSpDepr' => DB::select("CALL get_building_special_depreciations()"),
          'bldgStHt' => DB::select("CALL get_building_standard_heights()"),
          'bldgStrcMat' => DB::select("CALL get_building_structure_materials()"),
          'bldgType' => DB::select("CALL get_building_types()"),
          'bldgMrkVal' => DB::select("CALL get_building_unit_market_values()"),
          'bldgAsmtLvl' => DB::select("CALL get_building_class_assessment_levels()"),
          'bldgPosHolders' => DB::select("CALL get_position_holder_links('FAAS')")
        ]
      ]);
    } else {
      return json_encode([
        'success' => false,
        'res' => 'Invalid user'
      ]);
    }
  }

  private function checkAuth($header) {
    $test = new CheckRequestAuth();
    if($test->testToken($header)) {
      return true;
    } else {
      return false;
    }
  }
}
