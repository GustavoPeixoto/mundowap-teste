<?php

namespace Casts;

class Decimal implements Cast
{
	public static function cast($value, $args=[]) {
		if(isset($value)) {
			$value = OnlyNumeric::cast($value);
			$value = floatval($value);

			if (array_key_exists('precision', $args)) {
				$value = round($value, $args['precision']);
			}
		}

		return $value;
	}
}

?>