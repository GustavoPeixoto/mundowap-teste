<?php 

namespace Models;

use Casts\Integer;
use Casts\Decimal;
use Casts\Datetime;

class Produtos extends Model {
	protected static $table 		= 'produtos';
	protected static $primary 		= 'ean';
	protected static $autoIncrement = false;

	protected $fields = [
		'ean' 			=> null,
		'nome' 			=> null,
		'preco' 		=> null,
		'estoque' 		=> null,
		'fabricacao' 	=> null
	];

	protected $casts = [
		'ean' 			=> Integer::class,
		'preco' 		=> Decimal::class,
		'estoque' 		=> Integer::class,
		'fabricacao' 	=> Datetime::class
	];
}

?>