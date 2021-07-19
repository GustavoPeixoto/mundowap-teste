<?php

namespace Casts;

class CustomRegex implements Cast
{
	public static function cast($value, $args=[]) {
		if (!array_key_exists('regex', $args)) throw new \Exception(
			"Regex argument is required for this cast");
		return isset($value) ? preg_replace($args['regex'], '', $value) : $value;
	}
}

?>