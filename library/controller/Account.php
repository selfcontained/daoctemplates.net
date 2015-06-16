<?php
namespace Controller;

class Account extends Base {

	public function login($params) {
		$username = $this->param('username');
		$password = $this->param('password');

		$user = \Model\UserMapper::Authenticate($username, $password);

		if($user !== false) {
			$_SESSION['user'] = $user;
			$url = '/user/'.$user->id;
		}else {
			unset($_SESSION['user']);
			$url = '/?login=epicfail';
		}
		header('Location: '.$url);
	}

	public function logout($params) {
		unset($_SESSION['user']);
		header('Location: /');
	}

	public function edit($params) {
		$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

		if(!$user) {
			return header('Location: /');
		}

		$this->view->set('user', $user);
		$pass = $this->param('pass');
		if($pass == 'epicfail') {
			$this->view->set('error', "Passwords don't match");
		}else if($pass == 'epicerror') {
			$this->view->set('message', "Error updating password");
		}else if($pass == 'epicwin') {
			$this->view->set('message', "Password updated");
		}
		if($this->param('create') === 'epicwin') {
			$this->view->set('message', "Account created successfully");
		}

		$this->view->display('account/edit.twig');
	}

	public function save($params) {
		$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

		if(!$user) {
			return header('Location: /');
		}

		$pass1 = $this->param('newPassword');
		$pass2 = $this->param('verifyPassword');

		if($pass1 == null || $pass1 != $pass2) {
			return header('Location: /account?pass=epicfail');
		}

		$user->password = $pass1;
		$updated = \Model\UserMapper::updatePassword($user);

		if($updated) {
			return header('Location: /account?pass=epicwin');
		}else {
			return header('Location: /account?pass=epicerror');	
		}

	}

	public function create($params) {
		$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

		if($user) {
			return header('Location: /');
		}

		$user = new \Model\User();
		$user->name = $this->param('name');
		$user->password = $this->param('password');
		$user->verifyPassword = $this->param('verifyPassword');

		$this->view->set('user', $user);

		if($user->isValid()) {
			$created = \Model\UserMapper::create($user);

			if($created) {
				$_SESSION['user'] = $user;
				header('Location: /account?create=epicwin');
			}else {
				$this->view->set('error', 'something went kaboom, sry');
				return $this->view->display('account/new.twig');
			}

		}else {

			$errors = implode('<br>', $user->getErrors());
			$this->view->set('error', $errors);
			return $this->view->display('account/new.twig');

		}

	}

}