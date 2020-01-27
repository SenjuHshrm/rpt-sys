<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CheckRequestAuth;

class AssessLand extends Controller
{
    public function addLand(Request $req) {
			$header = $req->header('Authorization');
			$test = new CheckRequestAuth();
			if($test->testToken($header)) {
				try {
					$this->procAdd($req);
					return json_encode([ 'res' => true ]);
				} catch (Exception $e) {
					return json_encode([ 'res' => false ]);
				}
			} else {
				return json_encode([ 'res' => false ]);
			}
		}

		public function updateLand(Request $req) {
			if($req) {
				return json_encode(['res'=>'update']);
			}
		}

		private function procAdd($obj) {
			$query = "CALL assess_land_faas('".$obj['trnsCode']."', '".$obj['arpNo']."',".
							"'".$obj['pin']['city']."', '".$obj['pin']['district']."', '".$obj['pin']['barangay']."',".
							"'".$obj['pin']['section']."', '".$obj['pin']['parcel']."',".
							"'".$obj['OCT_TCT']."', '".$obj['surveyNo']."', '".$obj['lotNo']."', '".$obj['blockNo']."',".
							"'".$obj['propLoc']['streetNo']."', '".$obj['propLoc']['brgy']."', '".$obj['propLoc']['subd']."', '".$obj['propLoc']['city']."',".
							"'".$obj['propLoc']['province']."',".
							"'".$obj['propLoc']['north']."', '".$obj['propLoc']['south']."', '".$obj['propLoc']['west']."', '".$obj['propLoc']['east']."',".
							"'".$obj['landAppraisal']['class']."', '".$obj['landAppraisal']['subCls']."', ".$obj['landAppraisal']['interiorLot'].", ".$obj['landAppraisal']['cornerLot'].",".
							"".$obj['landAppraisal']['area'].", ".$obj['landAppraisal']['unitVal'].", ".$obj['landAppraisal']['baseMarketVal'].",".
							"".$obj['landAppraisal']['baseMarketVal'].",".$obj['landAppraisal']['stripping'].",".
							"'".$obj['propAsmt']['actualUse']."', ".$obj['propAsmt']['marketVal'].", ".$obj['propAsmt']['assessmentLvl'].",".
							"".$obj['propAsmt']['assessedVal'].", ".$obj['propAsmt']['specialClass'].", ".$obj['propAsmt']['total'].",".
							"'".$obj['propAsmt']['status']."',".
							"".$obj['propAsmt']['effty'].", '".$obj['propAsmt']['efftQ']."',".
							"'".$obj['propAsmt']['appraisedName']."', '".$obj['propAsmt']['appraisedDate']."',".
							"'".$obj['propAsmt']['recommendName']."', '".$obj['propAsmt']['recommendDate']."',".
							"'".$obj['propAsmt']['approvedName']."', '".$obj['propAsmt']['approvedDate']."',".
							"'".$obj['propAsmt']['memoranda']."',".
							"'".$obj['supersededRec']['supPin']."', '".$obj['supersededRec']['supArpNo']."',".
							"".$obj['supersededRec']['supTotalAssessedVal'].", '".$obj['supersededRec']['supPrevOwner']."',".
							"'".$obj['supersededRec']['supEff']."', '".$obj['supersededRec']['supARPageNo']."',".
							"'".$obj['supersededRec']['supRecPersonnel']."', '".$obj['supersededRec']['supTDNo']."',".
							"'".$obj['supersededRec']['supDate']."',".
							"'".$obj['status']."', ".$this->getEncoderId($obj['encoder']).",".
							"'".$obj['attachment']."')";
			$res = DB::select($query);
			$newId = $res[0]->id;
			$this->addOwners($obj->ownerDetails, $newId);
			$this->addAdmins($obj->adminDetails, $newId);
			$this->addStrips($obj->stripSet, $newId);
			$this->addImp($obj->othImp, $newId);
			$this->addAdjFct($obj->marketVal, $newId);
			return $newId;
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
					DB::select("CALL add_land_faas_improvements('".$impLs['kind']."', '".$impLs['totalNo']."', '".$impLs['unitVal']."', '".$impLs['baseMarkVal']."', '".$id."')");
				}
			}
		}

		private function addAdjFct($adjFctrs, $id) {
			if(count($adjFctrs) > 0) {
				foreach($adjFctrs as $adjF) {
					DB::select("CALL ad_land_faas_adjustment_factor('', '', '', '', '".$id."')");
				}
			}
		}



		private function getEncoderId($username) {
			$q = DB::select("CALL login_web('".$username."')");
			return $q[0]->user_id;
		}
}
