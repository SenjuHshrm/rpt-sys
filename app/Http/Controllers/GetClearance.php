<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class GetClearance extends Controller
{
    public function getFile(Request $request) {
        try {
            $cont = File::get(public_path().'\\storage\\'.$request['file']);
            return [ 'file' => base64_encode($cont) ];
            // return [ 'file' => base64_encode($cont) ];
        } catch(Exception $e) {
            return [ 'file' => false];
        }
    }
}
