<?php

namespace App\Http\Controllers;
use App\Classes\genJWT;

use Illuminate\Http\Request;

class CheckRequestAuth extends Controller
{
  public function testToken($header) {
		$proc = new genJWT();
		if($header) {
			$tokenRes = $proc->authToken(str_replace('Bearer ', '', $header));
			if($tokenRes) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
