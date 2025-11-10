<?php
require_once "/app/lib/Database.php";
require_once "/app/lib/Session.php";
require_once "/app/lib/request_data.php";
require_once "/app/lib/Filter.php";
require_once "/app/lib/is_identifier.php";
require_once "/app/lib/uuidv4.php";

$db = new Database();
$session = new Session();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
	http_response_code(400);
	die('Solo se admiten peticiones GET');
}

$filter = null;
if (isset($_GET['filter']) && $_GET['filter'] !== '') {
	$filter = $_GET['filter'];
}

if (!is_null($filter) && !is_identifier($filter)) {
	http_response_code(400);
	die('Nombre de filtro inválido');
}

$parent_id = null;
if (isset($_GET['parent']) && $_GET['parent'] !== '') {
	$parent_id = $_GET['parent'];
}

if (!is_null($parent_id) && !is_uuid($parent_id)) {
	http_response_code(400);
	die('ID de publicación inválido');
}

if (!$session->isLoggedIn() && !is_null($filter)) {
	http_response_code(400);
	die('No se permite el uso de filtros en cuentas de invitado');
}

$loaded_filter = null;
if (!$session->isLoggedIn() || is_null($filter)) {
	$loaded_filter = new Filter([[]], 'latest');
} else {
	$db_filter = $db->statement(
		'select pf_condition, sort_by from post_filters where name = :name and author = :author',
		[
			'name' => $filter,
			'author' => $session->get('username'),
		]
	);

	if (count($db_filter) === 0) {
		http_response_code(400);
		die('No existe un filtro con ese nombre');
	}

	$loaded_filter = new Filter(json_decode($db_filter[0]['pf_condition'], true), $db_filter[0]['sort_by']);
}

$stmt = $loaded_filter->toSQL($parent_id, $session->get('username'));

$posts = $db->statement($stmt['sql'], $stmt['parameters']);

echo json_encode([
	'parent_id' => $parent_id,
	'amount' => count($posts),
	'children' => array_map(
		fn($p) => [
			'id' => $p['id'],
			'author' => $p['author'],
			'posted_at' => $p['created_at'],
			'content' => $p['content'],
			'score' => intval($p['score']),
			'stance' => $p['stance'],
		],
		$posts
	),
]);
?>
