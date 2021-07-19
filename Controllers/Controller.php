<?php

namespace Controllers;

use Modules\Response;
use Modules\JWT;
use Modules\Database;

use Models\Logins;
use Models\Usuarios;

abstract class Controller {

	protected $request 		= [];
	protected $files 		= [];
	protected $headers 		= [];
	protected $method 		= [];
	protected $token 		= null;
	protected $usuario 		= null;
	protected $login 		= null;
	protected $authfree 	= [];
	protected $database 	= [];

	public function __construct() {
		$this->database = Database::getInstance();

		$this->files = $_FILES;

		$this->method = $_SERVER['REQUEST_METHOD'];
		if ($this->method == 'GET') $this->request = $_GET;
		else $this->request = json_decode(file_get_contents("php://input"), true);
		if (!isset($this->request)) $this->request = [];

		$this->setHeaders();
	}

	private function setHeaders() {
		foreach($_SERVER as $key => $value) {
			if (substr($key, 0, 5) == 'HTTP_') {
				$header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
				$this->headers[$header] = $value;
			}
		}
	}

	public function call($method) {
		if(method_exists($this, $method)) {
			if (array_search($method, $this->authfree) === false) {
				$auth = $this->middleware();
				if ($auth instanceof Response) return $auth;
			}

			return $this->$method();
		}
		return new Response(['error' => 'Method '.get_class($this).'->'.$method.' is not callable'], 500);
	}

	private function middleware(){
		$required = array('Accept', 'Authorization', 'User-Agent');
		foreach($required as $key) {
			if (!array_key_exists($key, $this->headers)) return new Response([
				'error' => 'Request without '.$key.' header'
			], 401);
		}

		if ($this->headers['Accept'] !== 'application/json') return new Response([
			'error' => 'Request Accept header must be application/json.',
			'given' => $this->headers['Accept']
		], 401);

		if (substr($this->headers['Authorization'], 0, 7) != 'Bearer ') return new Response([
			'error' => 'Authorization header must be a Bearer Token'
		], 401);

		$token = substr($this->headers['Authorization'], 7);

		$jwt 		= new JWT();
		$decoded 	= $jwt->validate($token);
		if ($decoded === false) return new Response([
			'error' => 'Invalid Token'
		], 401);

		$login = Logins::get('token = ?', [$token]);
		if (empty($login)) return new Response([
			'error' => "Couldn't find token"
		], 401);

		if ($decoded['expired'] === true) {
			$login = $login[0];

			$this->database->begin_transaction();
			try {
				$login->delete(); 

				$this->database->commit();

			} catch (\Exception $e) { $this->database->rollback(); throw $e; }

			return new Response([
				'error' => 'Expired Token'
			], 401);
		}

		$usuario = Usuarios::get('id = ?', [$decoded['payload']['id']]);
		if (empty($usuario)) return new Response([
			'error' => "Couldn't identify user on token"
		], 401);
		$this->usuario 	= $usuario[0];
		$this->login 	= $login[0];
		$this->token 	= $token;
	}
}

?>