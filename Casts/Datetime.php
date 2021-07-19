<?php

namespace Casts;

class Datetime implements Cast
{
	public static function cast($value, $args=[]) {
		if (!isset($value)) return $value;
		$args['format'] = array_key_exists('format', $args) ? $args['format'] : "Y-m-d H:i:s";

		$value = str_replace("/", "-", $value);
		$value = date_create(date('Y-m-d', strtotime($value)));
		if ($value === false) return null;
		return date_format($value, $args['format']);
	}
}

?>