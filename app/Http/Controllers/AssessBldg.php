<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssessBldg extends Controller
{
	public function addBldg(Request $req) {
		if($req) {
			return json_encode(['res'=>'add']);
		}
	}

	public function updateBldg(Request $req) {
		if($req) {
			return json_encode(['res'=>'update']);
		}
	}
}
