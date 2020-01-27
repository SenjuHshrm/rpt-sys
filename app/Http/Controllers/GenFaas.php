<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Classes\pdf\tcpdf\TCPDF;
use App\Classes\pdf\tcpdi\TCPDI;
use App\Http\Controllers\ChekRequestAuth;
use App\Http\Controllers\GenLandFaasCtrl;
use App\Http\Controllers\GenBldgFaasCtrl;

class GenFaas extends Controller
{
		private function checkAuth(Request $req) {
			$header = $req->header('Authorization');
			$token = new CheckRequestAuth();
			return $token->testToken($header);
		}

    public function genLandFaas(Request $req) {
			if(!GenFaas::checkAuth($req)) {
				return json_encode([ 'res' => false ]);
			} else {
				$gen = new GenLandFaasCtrl();
				return json_encode([ 'res' => $gen->genFile($req) ]);
			}
		}

		public function genBldgFaas(Request $req) {
			if(!$this->checkAuth($req)) {
				return json_encode([ 'res' => false ]);
			} else {
				$gen = new GenBldgFaasCtrl();
				return json_encode([ 'res' => $gen->genFile($req) ]);
			}
		}
}
