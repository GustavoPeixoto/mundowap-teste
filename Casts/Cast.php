<?php

namespace Casts;

interface Cast {
	public static function cast($value, $args=[]);
}

?>