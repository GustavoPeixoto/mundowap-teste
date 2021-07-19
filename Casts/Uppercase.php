<?php

namespace Casts;

class Uppercase implements Cast
{
	public static function cast($value, $args=[]) {
		return isset($value) ? mb_strtoupper($value) : $value;
	}
}

?>