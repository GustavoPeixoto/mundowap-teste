<?php

namespace Modules;

use Models\Usuarios;

class JWT {
	private $secret;
	private $hours;
	private $algorithm = 'sha256';

	private $header;

	public function __construct() {
		$settings 			= SettingsJson::getInstance();
		$this->secret 		= $settings->jwt['secret'];
		$this->hours 		= $settings->jwt['hours'];

		$this->header = array(
			'alg' => 'HS256',
			'typ' => 'JWT'
		);
		$this->header = json_encode($this->header);
		$this->header = base64_encode($this->header);
	}

	public function create(Usuarios $usuario) {
		$createDate = date('YmdHis');
		$expireDate = date('YmdHis', strtotime($createDate. ' + '.$this->hours.' hours'));

		$payload = array(
			'id' 	=> $usuario->id,
			'name' 	=> $usuario->nome,
			'login' => $usuario->username,
			'iat' 	=> $createDate,
			'exp' 	=> $expireDate
		);

		$payload 	= json_encode($payload, JSON_UNESCAPED_UNICODE);
		$payload 	= base64_encode($payload);

		$signature 	= hash_hmac($this->algorithm, $this->header.$payload, $this->secret, true);
		$signature 	= base64_encode($signature);

		return $token = $this->header.'.'.$payload.'.'.$signature;
	}

	public function validate($token) {
		$part 		= explode(".",$token);
		$header 	= $part[0];
		$payload 	= $part[1];
		$signature 	= $part[2];

		$valid = hash_hmac($this->algorithm, $header.$payload, $this->secret, true);
		$valid = base64_encode($valid);

		if ($signature != $valid) return false;

		$response = array(
			'header' 	=> json_decode(base64_decode($header), 	TRUE),
			'payload' 	=> json_decode(base64_decode($payload), TRUE),
			'expired' 	=> false,
		);

		$response['expired'] = strtotime(date('YmdHis')) > strtotime($response['payload']['exp']);

		return $response;
	}
}

?>