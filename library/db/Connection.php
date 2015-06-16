<?php
namespace DB;

class Connection {

	protected static $pdo;

	private static $schema;

	private static $username;

	private static $password;

	public static function setCredentials($schema, $username, $password) {
		self::$schema = $schema;
		self::$username = $username;
		self::$password = $password;
	}

	public static function getPdo() {
		if(self::$pdo) return self::$pdo;

		self::$pdo = new \PDO(
			'mysql:host=localhost;dbname='.self::$schema,
			self::$username,
			self::$password
		);

		return self::$pdo;
	}
}
