<?php

namespace Modules;

class Util {
	// valida as chaves de um array recursivamente
	public static function validate_array_keys($array, $keys, $name, $path=[]) {
		foreach ($keys as $key=>$value) {
			if (!array_key_exists($key, $array)) throw new \OutOfBoundsException(
			"Couldn't get \"".implode(":", [...$path, $key])."\" on $name");
			if (is_array($value)) self::validate_array_keys($array[$key], $value, $name, [...$path, $key]);
		}
	}

	public static function listToModel($list, $modelClass){
		$result = array();
		foreach($list as $item) array_push($result, new $modelClass($item));
		return $result;
	}

	public static function listToArray($list){
		$result = array();
		foreach($list as $item) { array_push($result, $item->toArray()); }
		return $result;
	}

	public static function getURI(){
		$url = str_replace($_SERVER['CONTEXT_DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']);
		$url = explode('/', $url);
		array_pop($url);
		$url = implode('/', $url);
		// $url = $_SERVER['REQUEST_URI'];
		$url 	= str_replace($url, '', $_SERVER['REQUEST_URI']);
		$url 	= explode('?', $url)[0];
		$url 	= explode('/', $url);
		array_splice($url, 1, 1);
		if (empty($url[sizeof($url) - 1])) array_pop($url);

		$uri = array(
			'prefix' 	=> implode('/', array_slice($url, 1, 1)),
			'sulfix' 	=> implode('/', array_slice($url, 2)),
			'url' 		=> implode('/', $url)
		);

		// $url = implode('/', $url);
		return $uri;
	}
}

?>