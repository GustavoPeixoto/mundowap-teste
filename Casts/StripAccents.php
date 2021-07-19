<?php

namespace Casts;

class StripAccents implements Cast
{
	public static function cast($value, $args=[]) {
		return isset($value) ? self::strip_accents($value) : $value;
	}

	protected static function strip_accents($value) {
		$strChars = array( 'a', 'e', 'i', 'o', 'u', 'c' );
		$replaces = array(
			array('char' => 'a', 'regex' => '(a|á|à|ã|â|ä)'	),
			array('char' => 'e', 'regex' => '(e|é|è|ê|ë)'	),
			array('char' => 'i', 'regex' => '(i|í|ì|î|ï)'	),
			array('char' => 'o', 'regex' => '(o|ó|ò|õ|ô|ö)'	),
			array('char' => 'u', 'regex' => '(u|ú|ù|û|ü)'	),
			array('char' => 'c', 'regex' => '(c|ç)'			)
		);

		foreach ($replaces as $replace) {
			$value = preg_replace('/('.$replace['regex'].')/ui', $replace['char'], $value);
		}

		return $value;
	}
}

?>