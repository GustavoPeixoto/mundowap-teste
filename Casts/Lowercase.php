<?php

namespace Casts;

class Lowercase implements Cast
{
	public static function cast($value, $args=[]) {
		return isset($value) ? mb_strtolower($value) : $value;
	}
}

?>