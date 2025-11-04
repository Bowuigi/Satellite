<?php
require_once "/app/lib/Database.php";

// Every method in this class performs side effects
class Session {
	public function __construct() {
		ini_set('session.cookie_httponly', 1);
		ini_set('session.use_strict_mode', 1);
		ini_set('session.cookie_samesite', 'Lax');
		// NOTE: Set to 1 on production to enable HTTPS-only authentication
		ini_set('session.cookie_secure', 0);

		session_start();
	}

	public function set(string $key, string|bool $value) {
		$_SESSION[$key] = $value;
	}

	public function get(string $key): string|bool|null {
		return $_SESSION[$key] ?? null;
	}

	public function login(string $username) {
		session_regenerate_id(true);
		$this->set('logged_in', true);
		$this->set('username', $username);
		// No need to verify the login server-side since the session files are already stored in the server
	}

	public function logout() {
		session_unset();
		session_destroy();
	}

	public function isLoggedIn(): bool {
		return $this->get('logged_in') ?? false;
	}
}
?>
