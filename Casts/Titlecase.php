<?php

namespace Casts;

class Titlecase implements Cast
{
	public static function cast($value, $args=[]) {
		return isset($value) ? self::strtotitle($value) : $value;
	}

	private static function strtotitle($value) {
		// separando pelos espacos
		$splited = explode(' ', trim($value));

		// forcando titlecase na primeira palavra
		$tmp = mb_strtolower($splited[0]);
		$result = [mb_strtoupper(mb_substr($tmp, 0, 1)).mb_substr($tmp, 1)];
		array_splice($splited, 0, 1);

		// aplicando titlecase nas demais palavras que tem mais de 2 caracteres
		foreach($splited as $item) {
			$tmp = mb_strtolower($item);
			$len = mb_strlen($tmp);

			if ($len == 1 || ($len == 2 && $tmp[0] == 'd')) {
				array_push($result, $tmp);
			}
			else {
				array_push($result, mb_strtoupper(mb_substr($tmp, 0, 1)).mb_substr($tmp, 1));
			}
		}
		
		return implode(' ', $result);
	}
}

?>