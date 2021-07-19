<?php

namespace Casts;

class Integer implements Cast
{
	public static function cast($value, $args=[]) {
		return isset($value) ? intval($value) : $value;
	}
}

?>