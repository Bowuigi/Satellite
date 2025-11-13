<?php
require_once "/app/lib/Database.php";
require_once "/app/lib/Session.php";

$db = new Database();
$session = new Session();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
	http_response_code(400);
	die('Solo se admiten peticiones GET');
}

if (!$session->isLoggedIn()) {
	echo json_encode([
		'logged_in' => false,
	]);
	exit;
}

$name = $session->get('username');

$filters = $db->statement(
	'select name from post_filters where author = :author',
	[ 'author' => $name ]
);

echo json_encode([
	'logged_in' => true,
	'name' => $name,
	'filter_amount' => count($filters),
	'filters' => array_map(fn($f) => $f['name'], $filters),
]);
?>
