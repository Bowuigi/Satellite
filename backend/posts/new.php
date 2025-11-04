<?php
require_once "/app/lib/Database.php";
require_once "/app/lib/Session.php";
require_once "/app/lib/request_data.php";
require_once "/app/lib/uuidv4.php";

$db = new Database();
$session = new Session();

if (!$session->isLoggedIn()) {
	http_response_code(400);
	die('No registrado');
}

$data = request_data(function(RequestJSON $json) {
	return [
		'parent' => $json['parent'],
		'content' => $json['content'],
	];
});

# Currently the text content limit is 5000 bytes (a medium-sized blog post in English)
# It can be changed without migrating the DB
if (strlen($data['content']) < 1 || strlen($data['content']) > 5000
  || !(is_null($data['parent']) || is_uuid($data['parent']))) {
	http_response_code(400);
	die('Publicación inválida');
}

if (!is_null($data['parent'])) {
	$parent_post = $db->statement(
		'select id from posts where id = :parent',
		['parent' => $data['parent']]
	);

	if (count($parent_post) !== 1) {
		http_response_code(400);
		die('Publicación inválida, la publicación padre no existe');
	}
}

$id = uuidv4();

$db->statement(
	'insert into posts (id, parent, author, created_at, content) values ( :id, :parent, :author, :date, :content )',
	[
		'id' => $id,
		'parent' => $data['parent'],
		'author' => $session->get('username'),
		'date' => date('Y-m-d h:i:s'),
		'content' => $data['content'],
	]
);

echo json_encode(['id' => $id]);
?>
