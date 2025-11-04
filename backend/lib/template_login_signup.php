<?php
require_once "/app/lib/Database.php";
require_once "/app/lib/Session.php";
require_once "/app/lib/request_data.php";

function template_login_signup(bool $is_login /* false on signup */) {
	$db = new Database();
	$session = new Session();

	$data = request_data(function(RequestJSON $json) {
		return [
			'username' => $json['username'],
			'password' => $json['password'],
		];
	});

	// Server side validation is simple, client-side is nicer to the user
	if ( !isset($data['username']) || !isset($data['password'])
	  || !preg_match('/^[a-z0-9_\\.]+$/u', $data['username'])
	  || !preg_match('/^.{6,100}$/u', $data['password'])) {
		http_response_code(400); // Bad request
		die('Credenciales inválidas');
	}


	$account = $db->statement(
		'select name, password_hash from users where name = :name',
		['name' => $data['username']]
	);

	if ($is_login) {
		if (count($account) === 0) {
			http_response_code(400);
			die('Usuario no encontrado');
		}

		if (!password_verify($data['password'], $account[0]['password_hash'])) {
			die('Contraseña incorrecta');
		}
	} else {
		if (count($account) !== 0) {
			http_response_code(400);
			die('El usuario ya existe');
		}

		$db->statement(
			'insert into users (name, password_hash, joined_at) values ( :name , :hash , :date )',
			[
				'name' => $data['username'],
				'hash' => password_hash($data['password'], PASSWORD_BCRYPT),
				'date' => date('Y-m-d H:i:s')
			]
		);
	}

	$session->login($data['username']);
}
?>
