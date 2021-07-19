<?php

namespace Modules;

class SettingsJson {
	private static $instance;

	private function __construct() {
		$settings = json_decode(file_get_contents("settings.json"), true);

		Util::validate_array_keys(
			$settings, 
			array(
				"database" 	=> array(
					"host" 		=> null, 
					"user" 		=> null, 
					"password" 	=> null, 
					"database" 	=> null
				),
				"jwt" 		=> array(
					"hours" 	=> null,
					"secret" 	=> null
				)
			), 
			"settings.json"
		);

		$this->database = $settings["database"];
		$this->jwt 		= $settings["jwt"];
	}

	public static function getInstance() {
		if (!isset(self::$instance)) self::$instance = new SettingsJson();
		return self::$instance;
	}
}

?>