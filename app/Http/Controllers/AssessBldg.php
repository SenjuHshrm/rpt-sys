<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CheckRequestAuth;

class AssessBldg extends Controller
{
	public function addBldg(Request $req) {
		$header = $req->header('Authorization');
		$test = new CheckRequestAuth();
		if($test->testToken($header)) {
			try {
				$this->procAdd($req);
				return json_encode([ 'res' => true ]);
			} catch(Exception $e) {
				return json_encode([ 'res' => false ]);
			}
		} else {
			return json_encode([ 'res' => false ]);
		}
	}

	private function procAdd($obj) {
		$query = "CALL assess_building_faas('".$obj['trnsCode']."', '".$obj['arpNo']."', '".$obj['pinBldg']."', ".
						"'".$obj['type']."', '".$obj['class']."', '".$obj['unitVal']."', ".
						"'".$obj['bldgPermitNo']."', '".$obj['bldgPermitIssueDate']."', ".
						"'".$obj['condoCertTitle']."', '".$obj['compCertIssueDate']."', ".
						"'".$obj['occIssueDate']."', '".$obj['constrDate']."', '".$obj['occDate']."', ".
						"'".$obj['aob']."', ".
						"'".$obj['storey']."', ".
						"'".$obj['roofMat']."', '".$obj['othRoofMat']."', ".
						"'".$obj['totalFlrArea']."', '".$obj['totalFlrCost']."', ".
						$obj['aiSubTotal'].", ".
						$obj['unpainted'].", ".$obj['secHandMat'].", '".$obj['bldgType']."', ".
						"'".$obj['bldgRate']."', ".
						$obj['bcConstCost'].", ".$obj['bcSubTotal'].", ".
						$obj['adSubTotal'].", ".$obj['totalConstCost'].", ".
						$obj['deprRate'].", ".$obj['deprCost'].", ".
						$obj['totalPrcDepr'].", ".$obj['deprMarkVal'].", ".
						"'".$obj['actualUse']."', ".$obj['marketVal'].", ".$obj['asmtLvl'].", ".
						$obj['specialClass'].", ".
						$obj['assessedVal'].", ".$obj['totalAssessedVal'].", '".$obj['status']."', ".
						$obj['effY'].", '".$obj['effQ']."', ".
						"'".$obj['appraisedBy']."', '".$obj['appraisedByDate']."', ".
						"'".$obj['recommending']."', '".$obj['recommendingDate']."', ".
						"'".$obj['approvedBy']."', '".$obj['approvedByDate']."', ".
						"'".$obj['memoranda']."', ".
						"'".$obj['supersededPin']."', '".$obj['supersededArpNo']."', ".
						$obj['supersededTotalAsdval'].", '".$obj['supersededPrevOwner']."', ".
						"'".$obj['superededEffA']."', ".
						"'".$obj['supersededRecPersonnel']."', '".$obj['supersededTDno']."', ".
						"'".$obj['supersededDate']."', ".
						"'".$obj['stat']."', '".$this->getEncoderId($obj['encoderId'])."', ".
						"'".$obj['attachment']."', ".
						$obj['landFaasId'].
		")";
		$res = DB::select($query);
    $newId = $res[0]->id;
    $this->addOwner($obj['owners'], $newId);
    $this->addAdmin($obj['admins'], $newId);
    $this->addFloors($obj['floors'], $newId);
    $this->addMVIncr($obj['additionalItems'], $newId);

	}

	public function updateBldg(Request $req) {
		if($req) {
			return json_encode(['res'=>'update']);
		}
  }

  private function addOwner($owners, $id) {
    if(count($owners) > 0) {
      foreach($owners as $ownerLs) {
        DB::select("CALL add_building_faas_owner('".$ownerLs['ownFName']."', '".$ownerLs['ownMName']."', '".$ownerLs['ownLName']."', '".$ownerLs['ownAddress']."', '".$ownerLs['ownContact']."', '".$ownerLs['ownTIN']."', ".$id.")");
      }
    }
  }

  private function addAdmin($admins, $id) {
    if(count($admins) > 0) {
      foreach($admins as $adminLs) {
        DB::select("CALL add_building_faas_administrator('".$adminLs['admFName']."', '".$adminLs['admMName']."', '".$adminLs['admLName']."', '".$adminLs['admAddress']."', '".$adminLs['admContact']."', '".$adminLs['admTIN']."', ".$id.")");
      }
    }
  }

  private function addFloors($floors, $id) {
    if(count($floors) > 0) {
      foreach($floors as $floorsLs) {
        DB::select("CALL add_building_faas_floor(".$floorsLs['floorNo'].", ".$floorsLs['area'].", '".$floorsLs['wallMat']."', '".$floorsLs['flooringMat']."', ".$floorsLs['floorHeight'].", ".$floorsLs['standardHeight'].", ".$floorsLs['adjBaseRate'].", '".$floorsLs['floorType']."', ".$id.")");
      }
    }
  }

  private function addMVIncr($incr, $id) {
    if(count($incr) > 0) {
      foreach($incr as $incrLs) {
        DB::select("CALL add_building_faas_additional_item('".$incrLs['addItem']."', '".$incrLs['subType']."', '".$incrLs['size']."', ".$incrLs['unitCost'].", ".$incrLs['totalCost'].", ".$id.")");
      }
    }
  }
  
  private function getEncoderId($username) {
    $q = DB::select("CALL login_web('".$username."')");
    return $q[0]->user_id;
  }
}
