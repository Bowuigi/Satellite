<?php
// This file is named `request_data` because the function of the same name is the main thing on this module, not the RequestJSON class

// Helper Array-like class to guarantee the request JSON is treated properly
class RequestJSON implements ArrayAccess {
	public function __construct(private array $data) {}

	public function offsetExists(mixed $offset): bool {
		return array_key_exists($offset, $this->data);
	}

	public function offsetGet(mixed $offset): mixed {
		if (!array_key_exists($offset, $this->data)) {
			throw new Exception('JSON key does not exist');
		}
		return $this->data[$offset];
	}

	public function offsetSet(mixed $offset, mixed $value): never {
		throw new Exception('Cannot modify request JSON');
	}

	public function offsetUnset(mixed $offset): never {
		throw new Exception('Cannot modify request JSON');
	}
}

// Grabs the entire POST request body, tries to parse it as JSON and uses a callback to extract expected fields
function request_data(callable $fun) {
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		http_response_code(400);
		die('Solo se admiten peticiones POST');
	}

	$body = file_get_contents('php://input');
	$json = json_decode($body, true);

	if ($json === null && json_last_error() !== JSON_ERROR_NONE) {
		http_response_code(400);
		die('JSON inválido');
	}
	try {
		return $fun(new RequestJSON($json));
	} catch (Throwable $exn) {
		if (str_contains($exn->getMessage(), 'JSON key')) {
			http_response_code(400);
			die('JSON sin los campos requeridos');
		} else if (str_contains($exn->getMessage(), 'JSON')) {
			http_response_code(400);
			die($exn->getMessage());
		} else {
			http_response_code(500);
			die($exn->getMessage());
		}
	}
}
?>
