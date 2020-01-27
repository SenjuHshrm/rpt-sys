<?php

namespace App\Classes;

use App\Classes\token;
use App\Classes\alg;
use Illuminate\Support\Facades\DB;

class genJWT {

    public $alg = 'HS256';
    public $typ = 'JWT';

    public function generate($username, $name, $type) {
        $data = new token();
        $data->username = $username;
        $data->name = $name;
        $data->iat = round(microtime(true) * 1000);
				$data->type = $type;
        $jwtToken = base64_encode(json_encode($this->header())) . '.' . base64_encode(json_encode($data));
        $sign = $this->sign($jwtToken);
        $response = $jwtToken . '.' . $sign;
        return $response;

    }

    private function header() {
        $res = new alg();
        $res->alg = $this->alg;
        $res->typ = $this->typ;
        return $res;
    }

    private function sign($payload) {
        return hash_hmac('sha256', $payload, env('JWT_SECRET'));
    }

    public function authToken($token) {
        if(strlen($token) == 0) {
            return false;
        } else {
						if($this->countTokenParts($token)) {
							return $this->testUser($token);
						} else {
							return false;
						}

        }
    }

		private function countTokenParts($token) {
			$parts = explode('.', $token);

			if(count($parts) !== 3) {
				return false;
			}

			$parts = array_filter(array_map('trim', $parts));

			if(count($parts) !== 3 || implode('.', $parts) !== $token) {
				return false;
			} else {
				return true;
			}
		}

		private function testUser($token) {
			$arr = explode('.', $token);
			$obj = json_decode(base64_decode($arr[1]));
			if(isset($obj)) {
				$res = DB::select("CALL login_web('".$obj->username."')");
				if(count($res) > 0) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

}
