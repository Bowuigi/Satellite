<?php
// Every method on this class performs side effects
class Database extends PDO {
	function __construct()  {
		try {
			// NOTE: Hide the password behind an environment variable / secret file in production
			parent::__construct('mysql:host=database;charset=utf8mb4', 'root', 'password');
			$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->perform_migrations();
		} catch (PDOException $exn) {
			http_response_code(500);
			error_log("Database error: " . $exn->getMessage()); // To debug log
			die("Internal server error");
		}
	}

	private function perform_migrations() {
		try {
			$this->query("create database if not exists satellite; use satellite;")->closeCursor();
			$this->query('call sys.table_exists("satellite", "users", @result); select @result;')->closeCursor();
			$sth = $this->query('select @result;');
			$result = $sth->fetch(PDO::FETCH_ASSOC)['@result'];
			$sth->closeCursor();
			// Version 0 (nothing) to latest
			if ($result === "") {
				$sql = file_get_contents("sql/version0.sql");
				$this->query($sql)->closeCursor();
			}
			// No more migrations
		} catch (PDOException $exn) {
			http_response_code(500);
			error_log("Database error: " . $exn->getMessage()); // To debug log
			die("Internal server error");
		}
	}

	public function statement(string $sql, array $values) {
		try {
			$stmt = $this->prepare($sql);
			$stmt->execute($values);
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $exn) {
			http_response_code(500);
			error_log("Database error: " . $exn->getMessage()); // To debug log
			die("Internal server error");
		}
	}
}
?>

