<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrgysSubd extends Controller
{
    public function get() {
			return json_encode([ 'res' => DB::select('CALL get_barangays_and_subdivisions()')]);
		}
}
