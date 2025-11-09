<?php
/*
* Even though there is a class in this file (ConditionData), the most important part of the module is the CONDITION_MAP variable
*/
require_once "/app/lib/is_identifier.php";

// Maps condition names to their corresponding validators/parsers, serializers and condition builders
// Morally a constant, but PHP does not allow constants that depend on runtime values
define('CONDITION_MAP', [
	'before' => [
		'set' => fn($s) => ConditionData::parseDate($s),
		'json' => fn($v) => $v?->format('Y-m-d'),
		'sql' => fn($p) => "created_at < {$p}",
	],
	'after' => [
		'set' => fn($s) => ConditionData::parseDate($s),
		'json' => fn($v) => $v?->format('Y-m-d'),
		'sql' => fn($p) => "created_at > {$p}",
	],
	// In days
	'minimum-age' => [
		'set' => fn($s) => ConditionData::validateGT0Int($s),
		'json' => fn($v) => $v,
		'sql' => fn($p) => "created_at <= now() - interval {$p} day",
	],
	// In days
	'maximum-age' => [
		'set' => fn($s) => ConditionData::validateGT0Int($s),
		'json' => fn($v) => $v,
		'sql' => fn($p) => "created_at >= now() - interval {$p} day",
	],
	'minimum-score' => [
		'set' => fn($s) => ConditionData::validateInt($s),
		'json' => fn($v) => $v,
		'sql' => fn($p) => "ifnull(score,0) >= {$p}",
	],
	'maximum-score' => [
		'set' => fn($s) => ConditionData::validateInt($s),
		'json' => fn($v) => $v,
		'sql' => fn($p) => "ifnull(score,0) <= {$p}",
	],
	'author' => [
		'set' => fn($s) => ConditionData::validateString($s, 'is_identifier'),
		'json' => fn($v) => $v,
		'sql' => fn($p) => "author = {$p}",
	],
	'not-author' => [
		'set' => fn($s) => ConditionData::validateString($s, 'is_identifier'),
		'json' => fn($v) => $v,
		'sql' => fn($p) => "author = {$p}",
	],
	// String between 1 and 5000 characters long (why would you want to search for a bigger string anyway?)
	// Case-insensitive substring match
	'contains' => [
		'set' => fn($s) => ConditionData::validateString($s,fn($u) => strlen($u) > 0 && strlen($u) < 5000),
		'json' => fn($v) => $v,
		'sql' => fn($p) => "locate({$p}, content) != 0",
	],
	// String between 1 and 5000 characters long (why would you want to search for a bigger string anyway?)
	// Case-insensitive substring match
	'lacks' => [
		'set' => fn($s) => ConditionData::validateString($s,fn($u) => strlen($u) > 0 && strlen($u) < 5000),
		'json' => fn($v) => $v,
		'sql' => fn($p) => "locate({$p}, content) = 0",
	],
]);

class ConditionData {
	public static function parseDate(mixed $str): DateTime {
		if (!is_string($str)) {
			throw new Exception('Filtro JSON inválido');
		}

		$value = null;
		try {
			$value = DateTime::createFromFormat('Y-m-d', $str);
			$value->setTime(0, 0);
			if (!$value) { throw new ValueError(); }
		} catch (ValueError) {
			throw new Exception('Filtro JSON inválido');
		}
		return $value;
	}

	public static function validateString(mixed $str, callable $condition): string {
		if (!is_string($str) || !$condition($str)) {
			throw new Exception('Filtro JSON inválido');
		}
		return $str;
	}

	public static function validateGT0Int(mixed $num): int {
		if (!filter_var($num, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
			throw new Exception('Filtro JSON inválido');
		}
		return $num;
	}

	public static function validateInt(mixed $num): int {
		if (!filter_var($num, FILTER_VALIDATE_INT)) {
			throw new Exception('Filtro JSON inválido');
		}
		return $num;
	}
}
?>
