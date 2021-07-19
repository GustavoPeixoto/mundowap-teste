<?php

namespace Casts;

class OnlyNumeric implements Cast
{
	public static function cast($value, $args=[]) {
		return isset($value) ? preg_replace('/[^0-9.]+/', '', $value) : $value;
	}
}

?>