<?php 

namespace Models;

use Casts\Integer;
use Casts\Datetime;

class Logins extends Model {
	protected static $table = 'logins';

	protected $fields = [
		'id' 			=> null,
		'token' 		=> null,
		'agent' 		=> null,
		'usuarios_id' 	=> null,
		'created_at' 	=> null
	];

	protected $casts = [
		'id' 			=> Integer::class,
		'usuarios_id' 	=> Integer::class,
		'created_at' 	=> Datetime::class
	];
}

?>