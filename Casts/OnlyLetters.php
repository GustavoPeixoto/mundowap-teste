<?php

namespace Casts;

class OnlyLetters implements Cast
{
	public static function cast($value, $args=[]) {
		return isset($value) ? preg_replace('/[^a-zA-Z]+/', '', $value) : $value;
	}
}

?>