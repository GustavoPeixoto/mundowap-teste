<?php

namespace Casts;

class FirstUpper implements Cast
{
	public static function cast($value, $args=[]) {
		if(isset($value)) {
			$value 		= mb_strtolower($value);
			$value[0] 	= mb_strtoupper($value[0]);
		}

		return $value;
	}
}

?>