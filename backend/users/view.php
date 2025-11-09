<?php
require_once "/app/lib/Database.php";

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
	http_response_code(400);
	die('Solo se admiten peticiones GET');
}
if (!isset($_GET['name']) || $_GET['name'] === '') {
	http_response_code(400);
	die('Falta el nombre del usuario a ver');
}

$db = new Database();

$users = $db->statement(
	'select joined_at from users where name = :name',
	['name' => $_GET['name']]
);

if (count($users) !== 1) {
	http_response_code(404);
	die('Usuario no encontrado');
}

$filters = $db->statement(
	'select name from post_filters where author = :author',
	[ 'author' => $_GET['name'] ]
);

echo json_encode([
	'name' => $_GET['name'],
	'joined_at' => $users[0]['joined_at'],
	'filter_amount' => count($filters),
	'filters' => $filters,
]);
?>
