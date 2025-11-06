<?php
/*
* Both conditions and filters are expected to be constructed inside a request_data callback
*/
require_once "/app/lib/CONDITION_MAP.php";

class Condition {
	private array $data = [];

	public function __construct($json) {
		if (!is_array($json)) {
			throw new Exception("Filtro JSON inválido");
		}

		foreach ($json as $key => $value) {
			$this->set($key, $value);
		}
	}

	public function set(string $key, mixed $value): void {
		if (!array_key_exists($key, CONDITION_MAP)) {
			throw new Exception("Filtro JSON inválido");
		}
		$this->data[$key] = CONDITION_MAP[$key]['set']($value);
	}

	public function toArray(): array {
		$result = [];
		foreach (CONDITION_MAP as $key => $opts) {
			if (!array_key_exists($key, $this->data)) continue;
			$result[$key] = $opts['json']($this->data[$key]);
		}
		return array_filter($result, fn($d) => !is_null($d));
	}

	public function toSQLCondition(int $counter): array {
		$sql = 'TRUE';
		$parameters = [];
		foreach (CONDITION_MAP as $key => $opts) {
			if (!array_key_exists($key, $this->data)) continue;
			$param = "builder_{$counter}";

			$sql = "{$sql} AND ({$opts['sql'](':' . $param)})";
			$parameters[$param] = $opts['json']($this->data[$key]);
			$counter += 1;
		}
		return ['sql' => $sql, 'parameters' => $parameters, 'counter' => $counter];
	}
}

class Filter {
	private array $conditions = [];

	public function __construct($json) {
		if (!is_array($json)) {
			throw new Exception("Filtro JSON inválido");
		}

		$this->conditions = array_map(fn($c) => new Condition($c), $json);
	}

	public function toArray(): array {
		return array_map(fn($c) => $c->toArray(), $this->conditions);
	}

	public function toSQLCondition(int $counter = 0) {
		$sql = 'FALSE';
		$parameters = [];
		foreach ($this->conditions as $cond) {
			$result = $cond->toSQLCondition($counter);

			$sql = "{$sql} OR ({$result['sql']})";
			$parameters = array_merge($parameters, $result['parameters']);
			$counter = $result['counter'];
		}
		return ['sql' => $sql, 'parameters' => $parameters, 'counter' => $counter];
	}

	public function toSQL() {
		$conditions = $this->toSQLCondition();
		return [
			'sql' => <<<SQL
with vote_counts as (
  select post, sum(case when is_upvote then 1 else -1 end) as score
  from votes group by post
)
select p.*, ifnull(vc.score, 0) as score
from posts as p left join vote_counts as vc on p.id = vc.post
where {$conditions['sql']}
SQL
			,'parameters' => $conditions['parameters'],
		];
	}
}
?>
