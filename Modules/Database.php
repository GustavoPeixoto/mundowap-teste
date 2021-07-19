<?php

namespace Modules;

class Database extends \mysqli{
	private static $instance;

	private function __construct() {
		$settings 			= SettingsJson::getInstance();
		$this->settings 	= $settings->database;
		parent::__construct(...array_values($this->settings));

		if ($this->connect_errno) throw new \Exception("Unable to connect to database! \n".$this->connect_error);

		mysqli_set_charset($this, 'utf8');
	}

	public static function getInstance() {
		if (!isset(self::$instance)) self::$instance = new Database();
		return self::$instance;
	}

	public function __destruct() {
		if (!$this->connect_errno) {
			$this->close(); 
		}
	}
	
	public function query($query, $resultmode = NULL) {
		$result = parent::query($query);
		if ($result === false) throw new \Exception("Query error: \n".$this->error);
		return $result;
	}

	public function statement($stmt, $fetch=true) {
		if ($stmt === false) throw new \Exception("Query error: \n".$this->error);
		$result = $stmt->execute();
		if ($result === false) throw new \Exception("Query error: \n".$this->error);

		return $fetch ? $stmt->get_result()->fetch_all(MYSQLI_ASSOC) : null;
	}

	public function prepare($sql) {
		$stmt = parent::prepare($sql);
		if ($stmt === false) throw new \Exception("Query error: \n".$this->error);
		return $stmt;
	}

	public function execStatement($sql, $values=[], $fetch=true) {
		$stmt 	= $this->prepare($sql);
		$stmt 	= $this->bindStatement($stmt, $values);
		return $this->statement($stmt, $fetch);
	}

	protected function bindStatement($stmt, $values) {
		if (empty($values)) return $stmt;

		$binds =  '';

		$types = array(
			'string' 	=> 's',
			'null' 		=> 's',
			'float' 	=> 'd',
			'double' 	=> 'd',
			'integer' 	=> 'i',
			'boolean' 	=> 'i',
		);

		foreach ($values as $value) {
			$binds .= $types[strtolower(gettype($value))];
		}

		$stmt->bind_param($binds, ...$values);

		return $stmt;
	}
}

?>