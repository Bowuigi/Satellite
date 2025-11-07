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

class SortOrder {
	private const ORDER_MAP = [
		'latest' => 'created_at desc',
		'oldest' => 'created_at asc',
		'upvotes' => 'ifnull(score, 0) desc',
		'downvotes' => 'ifnull(score, 0) asc',
	];
	public readonly string $order;
	public function __construct(mixed $str) {
		if (!is_string($str) || !array_key_exists($str, self::ORDER_MAP)) {
			throw new Exception('Filtro JSON inválido');
		}
		$this->order = $str;
	}

	public function toSQLSortOrder() {
		return "{$this::ORDER_MAP[$this->order]}, content desc";
	}
}

class Filter {
	private array $conditions = [];
	private SortOrder $sort_order;

	public function __construct($json, $sort_order) {
		if (!is_array($json)) {
			throw new Exception("Filtro JSON inválido");
		}
		$this->conditions = array_map(fn($c) => new Condition($c), $json);
		$this->sort_order = new SortOrder($sort_order);
	}

	public function toArray(): array {
		return [
			'condition' => array_map(fn($c) => $c->toArray(), $this->conditions),
			'sort_order' => $this->sort_order->order,
		];
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

	public function toSQL(string $parent_id) {
		$conditions = $this->toSQLCondition();
		$sort_order = $this->sort_order->toSQLSortOrder();
		$parent_condition = $parent_id === 'NULL' ? "parent is null" : "parent = {$parent_id}";

		return [
			'sql' => <<<SQL
with vote_counts as (
  select post, sum(case when is_upvote then 1 else -1 end) as score
  from votes group by post
)
select p.*, ifnull(vc.score, 0) as score
from posts as p left join vote_counts as vc on p.id = vc.post
where ({$parent_condition}) and ({$conditions['sql']})
order by {$sort_order}
SQL
			,'parameters' => $conditions['parameters'],
		];
	}
}
?>
