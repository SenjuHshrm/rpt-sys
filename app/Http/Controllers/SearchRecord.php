<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CheckRequestAuth;

class SearchRecord extends Controller
{
    public function search($sysCaller, $searchIn, $searchBy, $info, Request $request) {
        $header = $request->header('Authorization');
				$test = new CheckRequestAuth();
				if($test->testToken($header)) {
					if ($searchIn == 'land') {
						return [
							'success' => true,
							'err' => null,
							'data' => $this->searchLand($searchBy, $info, $sysCaller)
						];
					} else {
						return [
							'success' => true,
							'err' => null,
							'data' => $this->searchBldg($searchBy, $info, $sysCaller)
						];
					}
				} else {
					return [
						'success' => false,
						'err' => 'Invalid Token',
						'data' => null
					];
				}

    }

    private function searchLand($searchBy, $info, $sysCaller) {
        switch($searchBy){
            case 'pin':
                $q = $this->searchLandByPIN($info, $sysCaller);
                $owner = $this->getLandOwner($q);
                $admin = $this->getAdmin($q);
                return [
                    'faas' => $q,
                    'owner' => $owner,
                    'admin' => $admin,
                ];
                break;
            case 'arpNo':
                $q = $this->searchLandByARP($info, $sysCaller);
                $owner = $this->getLandOwner($q);
                $admin = $this->getAdmin($q);
                return [
                    'faas' => $q,
                    'owner' => $owner,
                    'admin' => $admin,
                ];
                break;
            case 'name':
                $data = $this->searchLandByName($info, $sysCaller);
                $owner = $this->getLandOwner($data);
                $admin = $this->getAdmin($data);
                return [
                    'faas' => $data,
                    'owner' => $owner,
                    'admin' => $admin,
                ];
                break;
        }
    }

    public function getLandOwner($qr) {
        $res = array();
        for($x = 0; $x < count($qr); $x++) {
            $q = DB::select("CALL get_land_faas_owners(".$qr[$x]->id.")");
            array_push($res, $q);
        }
        return $res;
    }

    public function getAdmin($qr) {
        $res = array();
        for($x = 0; $x < count($qr); $x++) {
            $q = DB::select("CALL get_land_faas_administrators(".$qr[$x]->id.")");
            array_push($res, $q);
        }
        return $res;
    }

    private function getBldgOwner($qr) {
        $res = array();
        for($x = 0; $x < count($qr); $x++) {
            $q = DB::select("CALL get_building_faas_owners(".$qr[$x]->id.")");
            array_push($res, $q);
        }
        return $res;
    }

    private function getAdminBldg($qr) {
        $res = array();
        for($x = 0; $x < count($qr); $x++) {
            $q = DB::select("CALL get_building_faas_administrators(".$qr[$x]->id.")");
            array_push($res, $q);
        }
        return $res;
    }

    public function searchLandByPIN($id, $sysCaller) {
        return DB::select("CALL search_land_faas_web('".$id."', 'PIN', '".$sysCaller."')");
    }

    public function searchLandByARP($id, $sysCaller) {
        return DB::select("CALL search_land_faas_web('".$id."', 'ARP_NO', '".$sysCaller."')");
    }

    public function searchLandByName($id, $sysCaller) {
        $res = DB::select("CALL search_land_faas_web('".$id."', 'NAME', '".$sysCaller."')");
        return $res;
    }

    private function searchBldg($searchBy, $info, $sysCaller) {
        switch($searchBy){
            case 'pin':
                $q = $this->searchBldgByPIN($info, $sysCaller);
                $owner = $this->getBldgOwner($q);
                $admin = $this->getAdminBldg($q);
                return [
                    'faas' => $q,
                    'owner' => $owner,
                    'admin' => $admin
                ];
                break;
            case 'arpNo':
                $q = $this->searchBldgByARP($info, $sysCaller);
                $owner = $this->getBldgOwner($q);
                $admin = $this->getAdminBldg($q);
                return [
                    'faas' => $q,
                    'owner' => $owner,
                    'admin' => $admin
                ];
                break;
            case 'name':
                $data = $this->searchBldgByName($info, $sysCaller);
                $owner = $this->getBldgOwner($data);
                $admin = $this->getAdminBldg($data);
                return [
                    'faas' => $data,
                    'owner' => $owner,
                    'admin' => $admin,
                ];
                break;
        }
    }

    private function searchBldgByPIN($id, $sysCaller) {
        return DB::select("CALL search_building_faas_web('".$id."', 'PIN', '".$sysCaller."')");
    }

    private function searchBldgByARP($id, $sysCaller) {
        return DB::select("CALL search_building_faas_web('".$id."', 'ARP_NO', '".$sysCaller."')");
    }

    private function searchBldgByName($id, $sysCaller) {
        return DB::select("CALL search_building_faas_web('".$id."', 'NAME', '".$sysCaller."')");
    }
}
