<?php

namespace Controllers;

use Modules\Response;
use Modules\JWT;

use Models\Usuarios;
use Models\Logins;

class AuthController extends Controller {

	protected $authfree = ['login'];

	/*
		Metodo HTTP: POST
		Recebe: 
			Nome de usuário: 	username (String)
			Senha do usuário: 	password (String)
		Acao:
			Gera um token de login para o usuário
		Retorna:
			{
				'message': 	{"tipo": "string", 	"descricao": "Mensagem de sucesso"},
				'token': 	{"tipo": "string", 	"descricao": "Token de login"}
			}
	*/
	public function login() {
		if (!array_key_exists('username', $this->request)) return new Response([
			'error' => 'Username é obrigatório!',
		], 400);

		if (!array_key_exists('password', $this->request)) return new Response([
			'error' => 'Password é obrigatório!',
		], 400);

		$usuario = Usuarios::get('username = ?', [$this->request['username']]);
		if (empty($usuario)) return new Response([
			'error' => 'Usuário não encontrado!',
		], 400);
		$usuario = $usuario[0];

		if (!password_verify($this->request['password'], $usuario->password)) return new Response([
			'error' => 'Senha incorreta!',
		], 400);

		$jwt 	= new JWT();
		$login 	= new Logins([
			'token' 		=> $jwt->create($usuario),
			'usuarios_id' 	=> $usuario->id,
			'agent' 		=> $this->headers['User-Agent']
		]);

		$this->database->begin_transaction();
		try {
			$login->save();

			$this->database->commit();

			return new Response([
				'message' 	=> 'Login com sucesso!',
				'token' 	=> $login->token
			], 200);
		} catch (\Exception $e) { $this->database->rollback(); throw $e; }
	}

	/*
		Metodo HTTP: POST
		Recebe: 
		Acao:
			Desloga o usuario do sistema
		Retorna:
			{
				'message': 	{"tipo": "string", 	"descricao": "Mensagem de sucesso"},
			}
	*/
	public function logout() {
		$this->database->begin_transaction();
		try {
			$this->login->delete();

			$this->database->commit();

			return new Response(['message' => 'Logout com sucesso!'], 200);
		} catch (\Exception $e) { $this->database->rollback(); throw $e; }
	}

	/*
		Metodo HTTP: POST
		Recebe: 
		Acao:
			Desloga todos os dispositvos do usuario do sistema
		Retorna:
			{
				'message': 	{"tipo": "string", 	"descricao": "Mensagem de sucesso"},
			}
	*/
	public function logoutAll() {
		$logins = Logins::get('usuarios_id = ?', [$this->usuario->id]);

		$this->database->begin_transaction();
		try {
			foreach($logins as $login) { $login->delete(); }

			$this->database->commit();

			return new Response(['message' => 'Logout com sucesso!'], 200);
		} catch (\Exception $e) { $this->database->rollback(); throw $e; }
	}

	/*
		Metodo HTTP: POST
		Recebe: 
		Acao:
			Valida o token do usuario
		Retorna:
			{
				'message': 	{"tipo": "string", 	"descricao": "Mensagem de sucesso"},
			}
	*/
	public function validate() {
		return new Response([
			'message' => 'Token válido!',
		], 200);
	}
}

?>
