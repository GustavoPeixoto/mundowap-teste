<?php

namespace Casts;

use Modules\TypesValidators;

class Boolean implements Cast
{
	public static function cast($value, $args=[]) {
		if (TypesValidators::validateInt($value)) {
			$value = intval($value);
			if ($value == 1) return true;
			else if ($value == 0) return false;
		}
		if (TypesValidators::validateStr($value)) {
			$value = strtolower($value);
			if ($value == 'true') return true;
			else if ($value == 'false') return false;
		}

		return $value;
	}
}

?>