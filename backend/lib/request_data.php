<?php
// Grabs the entire request body, tries to parse it as JSON and uses a callback to extract expected fields
function request_data(Callable $fun) {
	$body = file_get_contents('php://input');
	$json = json_decode($body, true);

	if ($json === null && json_last_error() !== JSON_ERROR_NONE) {
		http_response_code(400);
		die('Invalid JSON');
	}
	try {
		return $fun($json);
	} catch (Throwable $exn) {
		// Breaks if PHP changes its error message (unlikely), easier than validating a JSON schema
		if (str_contains($exn->getMessage(), 'Undefined array key')) {
			http_response_code(400);
			error_log("Request error: JSON without required fields " . json_encode($json));
			die('JSON without required fields');
		}
	}
}
?>
