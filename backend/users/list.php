<?php
require_once "/app/lib/Database.php";

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
	http_response_code(400);
	die('Solo se admiten peticiones GET');
}

$db = new Database();

$users = $db->statement('select name from users', []);

echo json_encode([
	'amount' => count($users),
	'results' => array_map(fn($u) => $u['name'], $users),
]);
?>
