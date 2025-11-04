<?php
require_once "/app/lib/Database.php";
require_once "/app/lib/Session.php";
require_once "/app/lib/request_data.php";
require_once "/app/lib/uuidv4.php";

$db = new Database();
$session = new Session();

$data = request_data(function(RequestJSON $json) {
	return [
		'post' => $json['post'],
		'stance' => $json['stance'],
	];
});

if (!in_array($data['stance'], ['up','down','none'], true) || !is_uuid($data['post'])) {
	http_response_code(400);
	die('Voto inválido');
}

$post = $db->statement(
	'select id from posts where id = :post',
	['post' => $data['post']]
);

if (count($post) !== 1) {
	http_response_code(400);
	die('Voto inválido, la publicación no existe');
}

if ($data['stance'] === 'none') {
	$db->statement(
		'delete from votes where post = :post and user = :user',
		[
			'post' => $data['post'],
			'user' => $session->get('username'),
		]
	);
} else {
	$db->statement(
		'insert into votes (post, user, is_upvote) values ( :post, :user, :upvote ) on duplicate key update is_upvote = :upvote',
		[
			'post' => $data['post'],
			'user' => $session->get('username'),
			'upvote' => ($data['stance'] === 'up') ? 1 : 0
		]
	);
}
?>
