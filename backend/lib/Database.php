<?php
// Every method on this class performs side effects
class Database extends PDO {
	function __construct()  {
		// NOTE: Hide the password behind an environment variable / secret file in production
		parent::__construct('mysql:host=127.0.0.1;charset=utf8mb4', 'root', 'password');
		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->perform_migrations();
	}
	private function perform_migrations() {
		$this->query("create database if not exists satellite; use satellite;")->closeCursor();
		$this->query('call sys.table_exists("satellite", "users", @result); select @result;')->closeCursor();
		$sth = $this->query('select @result;');
		$result = $sth->fetch()[0];
		$sth->closeCursor();
		// Version 0 (nothing) to latest
		if ($result === "") {
			$sql = file_get_contents("sql/version0.sql");
			$this->query($sql)->closeCursor();
		}
		// No more migrations
	}
}
?>

