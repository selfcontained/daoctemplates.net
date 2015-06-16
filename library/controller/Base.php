<?php
namespace Controller;

abstract class Base {

	protected $view;

	public function __construct() {
		$this->view = new \View\Twig(
			\Application\FrontController::getInstance()->getApplicationRoot().'/views'
		);
		$user = isset($_SESSION['user']) ? $_SESSION['user'] : false;
		$this->view->set('currentUser', $user);
	}

	public function param($name) {
		return isset($_REQUEST[$name]) ? $_REQUEST[$name] : null;
	}

}
