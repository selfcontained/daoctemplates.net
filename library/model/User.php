<?php
namespace Model;

class User {

	public $id, $name, $email, $created, $password;

	private $errors = array();

	public function isValid() {
		if(!$this->name) {
			$this->errors[] = 'Username must be set';
		}

		if(!UserMapper::isUnique($this)) {
			$this->errors[] = 'That username is already taken';
		}

		if(!$this->password) {
			$this->errors[] = 'Password must be set';
		}

		if($this->password !== $this->verifyPassword) {
			$this->errors[] = 'Passwords don\'t match';
		}

		return count($this->errors) === 0;
	}

	public function getErrors() {
		return $this->errors;
	}

}

class UserMapper {

	public static function fetch($id) {
		$sql = 'SELECT 
				users.UserID AS id, 
				users.UserName AS name, 
				users.Email AS email, 
				users.UserCreated AS created
			FROM users
			WHERE users.UserID = :id
		';
		$statement = \DB\Connection::getPdo()->prepare($sql);
		$statement->bindParam(':id', $id, \PDO::PARAM_INT);
		$statement->execute();
		$user = $statement->fetchObject('\Model\User');
		return $user;
	}

	public static function authenticate($username, $password) {

		$sql = 'SELECT 
			UserId AS id,
			UserName AS name,
			Email AS email,
			UserCreated AS created
			FROM users
			WHERE UserName = :name AND Password = :password
		';
		$statement = \DB\Connection::getPdo()->prepare($sql);
		$statement->bindParam(':name', $username);
		$hashedPassword = md5($password);
		$statement->bindParam(':password', $hashedPassword);
		$statement->execute();
		return $statement->fetchObject('\Model\User');
	}

	public static function updatePassword(\Model\User $user) {
		$sql = 'UPDATE users
			SET password = :password
			WHERE UserId = :id
		';
		$statement = \DB\Connection::getPdo()->prepare($sql);
		$statement->execute(array(
			':password'=> md5($user->password),
			':id' => $user->id
		));
		return $statement->rowCount();
	}

	public static function create(\Model\User $user) {

		$sql = 'INSERT INTO users
			(UserName,Password,UserCreated)
			VALUES (:name, :password, NOW())
		';
		$statement = \DB\Connection::getPdo()->prepare($sql);
		$statement->bindParam(':name', $user->name);
		$statement->bindParam(':password', md5($user->password));
		$statement->execute();
		$inserted = $statement->rowCount();
		if($inserted > 0) {
			$user->id = \DB\Connection::getPdo()->lastInsertId();
			return true;
		}else {
			return false;
		}
	}

	public static function isUnique($user) {
		$sql = 'SELECT count(0) FROM users WHERE UserName = :name';
		$statement = \DB\Connection::getPdo()->prepare($sql);
		$statement->bindParam(':name', $user->name);
		$statement->execute();
		return $statement->fetchColumn() == 0;
	}

}