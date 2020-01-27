<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\CheckRequestAuth;
use Illuminate\Support\Facades\DB;

class CheckPIN extends Controller
{
    public function checkLand(Request $request) {
        $success = false;
        $res = '';
        if($this->checkAuth($request->header('Authorization'))) {
          $q = "CALL check_pin_availability_land_faas_web('".$request['pin']."')";
          $result = DB::select($q);
          switch($result[0]->result) {
            case "inv_existing":
              $sucess = false;
              $res = 'Existing';
              break;
            case "inv_retired":
              $sucess = false;
              $res = 'Retired';
              break;
            case "inv_pending":
              $sucess = false;
              $res = 'Pending';
              break;
            default:
              $sucess = true;
              $res = 'Valid';
          }
        } else {
          $sucess = 'error';
          $res = 'Invalid User';
        }
        return json_encode([
          'success' => $sucess,
          'res' => $res
        ]);
    }

    public function checkBldg(Request $request) {
      $success = false;
      $res = '';
      if($this->checkAuth($request->header('Authorization'))) {
        $q = "CALL check_pin_availability_building_faas_web('".$request['pin']."')";
        $result = DB::select($q);
        switch($result[0]->result) {
          case "inv_existing":
            $sucess = false;
            $res = 'Existing';
            break;
          case "inv_retired":
            $sucess = false;
            $res = 'Retired';
            break;
          default:
            $sucess = true;
            $res = 'Valid';
        }
      } else {
        $sucess = 'error';
        $res = 'Invalid User';
      }
      return json_encode([
        'success' => $sucess,
        'res' => $res
      ]);
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
