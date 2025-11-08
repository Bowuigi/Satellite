<?php
require_once "/app/lib/Database.php";
require_once "/app/lib/Session.php";
require_once "/app/lib/request_data.php";
require_once "/app/lib/uuidv4.php";
require_once "/app/lib/Filter.php";

$db = new Database();
$session = new Session();

if (!$session->isLoggedIn()) {
	http_response_code(400);
	die('No registrado');
}

$data = request_data(function(RequestJSON $json) {
	$flt = new Filter($json['condition'], $json['sort_by']);
	return [
		'name' => $json['name'],
		'filter' => $flt,
	];
});

if (!preg_match('/^[a-z0-9\-_\\.]{1,50}$/', $data['name'])) {
	http_response_code(400);
	die('Nombre inválido');
}

$filters = $db->statement('select name from post_filters where name = ?', [$data['name']]);

if (count($filters) !== 0) {
	http_response_code(400);
	die('Ya existe un filtro con ese nombre');
}

$id = uuidv4();
$filter_array = $data['filter']->toArray();
$db->statement(
	'insert into post_filters (id, name, author, pf_condition, sort_by) values (:id, :name, :author, :condition, :sort)',
	[
		'id' => $id,
		'name' => $data['name'],
		'author' => $session->get('username'),
		'condition' => json_encode($filter_array['condition']),
		'sort' => "{$filter_array['sort_order']}",
	]
);
echo json_encode(['id' => $id]);
?>
