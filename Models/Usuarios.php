<?php 

namespace Models;

use Casts\Integer;
use Casts\Titlecase;
use Casts\Lowercase;
use Casts\Datetime;
use Casts\Boolean;

class Usuarios extends Model {
	protected static $table 		= 'usuarios';
	protected static $softDeletes 	= true;

	protected $fields = [
		'id' 			=> null,
		'nome' 			=> null,
		'username' 		=> null,
		'password' 		=> null,
		'admin' 		=> null,
		'created_at' 	=> null,
		'updated_at' 	=> null,
		'deleted_at' 	=> null
	];

	protected $hiddens = [
		'password'
	];

	protected $casts = [
		'id' 			=> Integer::class,
		'nome' 			=> Titlecase::class,
		'username' 		=> Lowercase::class,
		'admin' 		=> Boolean::class,
		'created_at' 	=> Datetime::class,
		'updated_at' 	=> Datetime::class,
		'deleted_at' 	=> Datetime::class,
	];

	protected $change = [
		'nome' 		=> 'changeNome',
	];

	protected function changeNome($value) {
		if (!isset($this->username)) {
			$this->username = str_replace(' ', '.', $value);
		}

		return $value;
	}
}

?>