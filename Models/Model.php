<?php 

namespace Models;

use Modules\Database;
use Modules\Util;

abstract class Model {
	protected $fields 				= [];
	protected $casts 				= [];
	protected $hiddens 				= [];
	protected $change 				= [];
	protected static $softDeletes 	= false;
	protected static $table 		= null;
	protected static $primary 		= 'id';
	protected static $autoIncrement = true;

	public function __construct(array $fields=[]) {
		foreach ($this->fields as $key=>$value) { 
			$this->$key = array_key_exists($key, $fields) ? $fields[$key] : null; 
		}
	}

	// fornece acesso valores de atributos virtuais
	public function __get($name) {
		// se é uma propriedade que já existe, apenas retorna o valor
		if (property_exists($this, $name)) return $this->$name;
		// se é o nome de um campo, retorna o valor do campo
		if (array_key_exists($name, $this->fields)) return $this->fields[$name];
		throw new \OutOfBoundsException(static::class . ' has no property $name!');
	}

	// permite setar valores para atributos virtuais
	public function __set($name, $value) {
		// se é uma propriedade que já existe, apenas seta o valor
		if (property_exists($this, $name)) { $this->$name = $value; return; }
		
		// se é o nome de um campo
		if (array_key_exists($name, $this->fields)) { 
			// se for string remove espacos do inicio e do final
			if (gettype($value) === 'string') $value = trim($value);

			// se existe um evento de alteracao para esse campo, dispara o evento
			if (array_key_exists($name, $this->change)) { 
				$function = $this->change[$name];
				$value = $this->$function($value);
			}

			// se existe um cast para esse campo, executa o cast
			if (array_key_exists($name, $this->casts)) { 
				$value = $this->casts[$name]::cast($value);
			}

			$this->fields[$name] = $value; 
			return; 
		}
		throw new \OutOfBoundsException(static::class . ' has no property $name!');
	}

	public function __isset($name) {
		return isset($this->fields[$name]);
	}

	// retorna os campos do model em formato de array
	public function toArray() { 
		$fields = $this->fields; 
		foreach($this->hiddens as $field) { unset($fields[$field]); }
		return $fields;
	}

	public static function get($whereStatements='', $whereValues=[]) {
		$database = Database::getInstance();
		$sql 	= "SELECT * FROM ".static::$table;

		if (static::$softDeletes === true) {
			if (empty($whereStatements)) {
				$whereStatements = 'deleted_at IS NULL';
			}
			else {
				$whereStatements = 'deleted_at IS NULL AND ('.$whereStatements.') ';
			}
			$sql .= " WHERE ".$whereStatements;
		}
		else if (!empty($whereStatements)) {
			$sql .= " WHERE ".$whereStatements;
		}

		$result = $database->execStatement($sql, $whereValues);
		return sizeof($result) > 0 ? Util::listToModel($result, static::class) : [];
	}

	// salva o registro
	public function save() {
		$database = Database::getInstance();

		$primaryKey = static::$primary;

		$values = array();

		$fields = $this->fields;
		// se tem auto increment, nao será passado com campo de chave primaria
		if (static::$autoIncrement === true) {
			unset($fields[$primaryKey]);
		}

		$values = array_values($fields);

		// se o registro nao existe, executa insert, se existe, executa update
		if (!$this->exists()) {
			// removendo campos com valor null
			$i=0;
			while ($i<sizeof($values)) {
				if (!isset($values[$i])) {
					array_splice($values, $i, 1);
					array_splice($fields, $i, 1);
				}
				else $i++;
			}

			$sql = "
			INSERT INTO ".static::$table." (".implode(', ', array_keys($fields)).") 
			VALUES (".implode(', ', array_map(function ($field) { return '?'; }, $fields)).");
			";

			$database->execStatement($sql, $values, false);

			if (static::$autoIncrement) $this->$primaryKey = $database->insert_id;
		}
		else {
			$sql = "
			UPDATE ".static::$table." SET ".implode(' = ?, ', array_keys($fields))." = ? 
			WHERE ".$primaryKey." = ?
			";

			array_push($values, $this->$primaryKey);

			$database->execStatement($sql, $values, false);
		}
	}

	// deleta o registro
	public function delete() {
		$database = Database::getInstance();
		
		$primaryKey = static::$primary;

		if (is_null($this->$primaryKey)) throw new \Exception(
			"Couldn't delete without set primary key");

		$values = array();

		if (static::$softDeletes === true) {
			$sql = "UPDATE ".static::$table." SET deleted_at = CURRENT_TIMESTAMP WHERE ".$primaryKey." = ?";
		}
		else {
			$sql = "DELETE FROM ".static::$table." WHERE ".$primaryKey." = ?";
		}

		array_push($values, $this->$primaryKey);

		$database->execStatement($sql, $values, false);
	}

	// consulta se o registro ja existe
	public function exists() {
		$primaryKey = static::$primary;
		if (is_null($this->$primaryKey)) return false;
		return sizeof(static::get($primaryKey.' = ?', [$this->$primaryKey])) > 0;
	}
}

?>