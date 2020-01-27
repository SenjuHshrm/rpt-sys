<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CheckRequestAuth;

class ReassessmentCtrl extends Controller
{
    public function reassessLand(Request $req) {

			$header = $req->header('Authorization');
			$test = new CheckRequestAuth();
			if($test->testToken($header)) {
				return json_encode([ 'res' => true ]);
			} else {
				return json_encode([ 'res' => false ]);
			}
		}

		public function reassessBldg(Request $req) {
			if($req) {
				return json_encode(['res'=>'add']);
			}
		}

		private function procUpdateLand($obj) {
			try {
				$query = "CALL assess_land_faas('".$obj['trnsCode']."', '".$obj['arpNo']."',".
								"'".$obj['pin']['city']."', '".$obj['pin']['district']."', '".$obj['pin']['barangay']."',".
								"'".$obj['pin']['section']."', '".$obj['pin']['parcel']."',".
								"'".$obj['OCT_TCT']."', '".$obj['surveyNo']."', '".$obj['lotNo']."', '".$obj['blockNo']."',".
								"'".$obj['propLoc']['streetNo']."', '".$obj['propLoc']['brgy']."', '".$obj['propLoc']['subd']."', '".$obj['propLoc']['city']."',".
								"'".$obj['propLoc']['province']."',".
								"'".$obj['propLoc']['north']."', '".$obj['propLoc']['south']."', '".$obj['propLoc']['west']."', '".$obj['propLoc']['east']."',".
								"'".$obj['landAppraisal']['class']."', '".$obj['landAppraisal']['subCls']."', '".$obj['landAppraisal']['interiorLot']."', '".$obj['landAppraisal']['cornerLot']."',".
								"'".$obj['landAppraisal']['area']."', '".$obj['landAppraisal']['unitVal']."', '".$obj['landAppraisal']['baseMarketVal']."',".
								"'".$obj['landAppraisal']['baseMarketVal']."',".
								"'".$obj['landAppraisal']['stripping']."',".
								"'".$obj['propAsmt']['actualUse']."', '".$obj['propAsmt']['marketVal']."', '".$obj['propAsmt']['assessmentLvl']."',".
								"'".$obj['propAsmt']['assessedVal']."', '".$obj['propAsmt']['specialClass']."', '".$obj['propAsmt']['total']."',".
								"'".$obj['propAsmt']['status']."',".
								"'".$obj['propAsmt']['effty']."', '".$obj['propAsmt']['efftQ']."',".
								"'".$obj['propAsmt']['appraisedName']."', '".$obj['propAsmt']['appraisedDate']."',".
								"'".$obj['propAsmt']['recommendName']."', '".$obj['propAsmt']['recommendDate']."',".
								"'".$obj['propAsmt']['approvedName']."', '".$obj['propAsmt']['approvedDate']."',".
								"'".$obj['propAsmt']['memoranda']."',".
								"'".$obj['supersededRec']['supPin']."', '".$obj['supersededRec']['supArpNo']."',".
								"'".$obj['supersededRec']['supTotalAssessedVal']."', '".$obj['supersededRec']['supPrevOwner']."',".
								"'".$obj['supersededRec']['supEff']."', '".$obj['supersededRec']['supARPageNo']."',".
								"'".$obj['supersededRec']['supRecPersonnel']."', '".$obj['supersededRec']['supTDNo']."',".
								"'".$obj['supersededRec']['supDate']."',".
								"'".$obj['status']."', ".$this->getEncoderId($obj['encoder']).",".
								"'".$obj['id']."')";
				DB::select($query);
				$this->addOwners($obj->ownerDetails, $obj['id']);
				$this->addAdmins($obj->adminDetails, $obj['id']);
				$this->addStrips($obj->stripSet, $obj['id']);
				$this->addImp($obj->othImp, $obj['id']);
				$this->addAdjFct($obj->marketVal, $obj['id']);
				return true;
			} catch(Exception $e) {
				return false;
			}
		}

		private function addOwners($owners, $id) {
			if(count($owners) > 0) {
				foreach($owners as $ownerLs) {
					DB::select("CALL add_land_faas_owner('".$ownerLs['ownFName']."', '".$ownerLs['ownMName']."', '".$ownerLs['ownLName']."', '".$ownerLs['ownAddress']."', '".$ownerLs['ownContact']."', '".$ownerLs['ownTIN']."', '".$id."')");
				}
			}
		}

		private function addAdmins($admins, $id) {
			if(count($admins) > 0) {
				foreach($admins as $adminLs) {
					DB::select("CALL add_land_faas_administrator('".$adminLs['admFName']."', '".$adminLs['admMName']."', '".$adminLs['admLName']."', '".$adminLs['admAddress']."', '".$adminLs['admContact']."', '".$adminLs['admTIN']."', '".$id."')");
				}
			}
		}

		private function addStrips($strips, $id) {
			if(count($strips) > 0) {
				foreach($strips as $strp) {
					DB::select("CALL add_land_faas_strip('".$strp['stripNum']."', ".$strp['stripArea'].", ".$strp['adjustment'].", ".$strp['stripMarkVal'].", ".$strp['adjustedBaseRate'].", '".$id."')");
				}
			}
		}

		private function addImp($imps, $id) {
			if(count($imps) > 0) {
				foreach($imps as $impLs) {

				}
			}
		}

		private function addAdjFct($adjFctrs, $id) {
			if(count($adjFctrs) > 0) {
				foreach($adjFctrs as $adjF) {

				}
			}
		}

		private function getEncoder($username) {
			$q = DB::select("CALL login('".$username."')");
			return $q[0]->user_id;
		}


}
