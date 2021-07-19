<?php

namespace Modules;

use Models\Model;

class TypesValidators {
	public static function validateDate($value){
		return (
			self::validateStr($value) 				&&
			preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", $value) 	&&
			checkdate(
				intval(substr($value, 5, 2)), 	# mes
				intval(substr($value, 8, 2)), 	# dia
				intval(substr($value, 0, 4))	# ano
			)
		);
	}

	public static function validateBool($value){
		return (gettype($value) 	== 'boolean');
	}

	public static function validateInt($value){
		$type = gettype($value);
		if ($type == 'integer') return true;
		else if ($type == 'string') $value = self::trimNumber($value);
		return strval(intval($value)) === $value;
	}

	public static function validateStr($value){
		return (gettype($value) 	== 'string');
	}

	public static function validateNumber($value){
		$type = gettype($value);
		if (array_search($type, array('integer', 'double', 'float')) !== false) return true;
		else if ($type == 'string') $value = self::trimNumber($value);
		return strval(floatval($value)) === $value;
	}

	public static function trimNumber($value){
		$sign = '';
		if ($value[0] == '-') { 
			$sign = $value[0];
			$value = substr($value, 1, strlen($value));
		}
		$value = ltrim($value, '0');

		$len = sizeof(explode('.', $value));
		if ($len > 2) return null;
		if ($len > 1) $value = rtrim($value, '0');

		if ($value == '') return '0';
		if ($value[0] == '.') $value = '0'.$value;
		if ($value[strlen($value)-1] == '.') $value = substr($value, 0, strlen($value)-1);

		return $sign.$value;
	}

	public static function validateList($value){
		return (gettype($value) 	== 'array');
	}

	public static function validateListType($list, $type){
		$errors = array();
		foreach ($list as $key => $value) if (gettype($value) != $type) array_push($errors, array($key=>$value));
		return $errors;
	}

	public static function validateListUnique($list){
		return array_diff_key($list, array_unique($list));
	}

	public static function validateListAssoc($list){
		if (empty($list)) return false;
		return array_keys($list) !== range(0, count($list) - 1);
	}

	public static function validateCpf($value){
		return (
			self::validateStr($value) 				&&
			sizeof(preg_replace('/[^0-9.]+/', '', $value)) == 11
		);
	}

	public static function castBool($value){
		if (self::validateInt($value)) {
			$value = intval($value);
			if ($value == 1) return true;
			else if ($value == 0) return false;
		}
		if (self::validateStr($value)) {
			$value = strtolower($value);
			if ($value == 'true') return true;
			else if ($value == 'false') return false;
		}

		return null;
	}

	public static function isModel($value) {
		return $value instanceof Model;
	}
}
?>
