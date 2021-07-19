<?php

namespace Controllers;

use Modules\Response;
use Modules\Util;
use Modules\TypesValidators;

use Models\Usuarios;

class UsuariosController extends Controller {

	/*
		Metodo HTTP: GET
		Recebe: 
			ID do usuario: id (Int)
		Acao:
			Busca usuarios no banco de dados
		Retorna:
			[
				{
					'id': 			{"tipo": "int", 		"descricao": "ID do usuario"},
					'nome': 		{"tipo": "string", 	"descricao": "Nome do usuario"},
					'username': 	{"tipo": "string", 	"descricao": "Login do usuario"},
					'admin': 		{"tipo": "bool", 	"descricao": "Permissoes de administrador"},
					'created_at': 	{"tipo": "datetime", "descricao": "Data de cadastro do usuario"},
					'updated_at': 	{"tipo": "datetime", "descricao": "Data de atualizacao do usuario"},
					'deleted_at': 	{"tipo": "datetime", "descricao": "Data de exclusao do usuario"}
				}
			]
	*/
	public function get() {
		$where = array(
			'stmt' 		=> '',
			'values' 	=> []
		);

		if (array_key_exists('id', $this->request)) {
			if (!TypesValidators::validateInt($this->request['id'])) return new Response([
				'error' => 'Id deve ser um inteiro'
			], 400);

			$where['stmt'] .= 'id = ?';
			array_push($where['values'], $this->request['id']);
		}

		return new Response(Util::listToArray(Usuarios::get( $where['stmt'], $where['values'])), 200);
	}

	/*
		Metodo HTTP: POST
		Recebe: 
			{
				'nome': 		{"tipo": "string", 		"descricao": "Nome do usuario"},
				'username': 	{"tipo": "string", 		"descricao": "Login do usuario"},
				'admin': 		{"tipo": "bool", 		"descricao": "Permissoes de administrador"}
			}
		Acao:
			Cadastra um usuario no banco de dados
		Retorna:
			[
				{"tipo": "int", "descricao": "ID do usuario cadastrado"}
			]
	*/
	public function create(){
		if ($this->usuario->admin !== true) return new Response([
			'message' 	=> 'Usuário sem permissões!'
		], 401);

		if (!array_key_exists('nome', 		$this->request)) return new Response([
			'error' => 'nome é obrigatório!'
		], 400);

		if (!array_key_exists('username', 	$this->request)) return new Response([
			'error' => 'username é obrigatório!'
		], 400);
		$usuario = Usuarios::get('username = ?', [$this->request['username']]);
		if (!empty($usuario)) return new Response([
			'error' => 'Este username já esta sendo usado!'
		], 400);

		if (!array_key_exists('password', 	$this->request)) return new Response([
			'error' => 'password é obrigatório!'
		], 400);

		if (!array_key_exists('admin', 		$this->request)) return new Response([
			'error' => 'admin é obrigatório!'
		], 400);

		if (!array_key_exists('admin', 		$this->request)) return new Response([
			'error' => 'admin é obrigatório!'
		], 400);
		if (!TypesValidators::validateBool($this->request['admin'])) return new Response([
			'error' => 'admin deve ser um booleano!'
		], 400);

		$usuario = new Usuarios([
			'nome' 			=> $this->request['nome'],
			'username' 		=> $this->request['username'],
			'password' 		=> password_hash($this->request['password'], PASSWORD_DEFAULT),
			'admin' 		=> $this->request['admin'],
		]);

		$this->database->begin_transaction();
		try {
			$usuario->save();

			$this->database->commit();

			return new Response([$usuario->id], 200);
		} catch (\Exception $e) { $this->database->rollback(); throw $e; }
	}
}

?>
