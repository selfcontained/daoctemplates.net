<?php
error_reporting(E_ERROR);
ini_set('display_errors',true);
set_include_path(get_include_path().PATH_SEPARATOR.'../library/');

require('application/Autoloader.php');
Application\Autoloader::register(parse_ini_file('../config/classmap.ini'));

$secrets = parse_ini_file('../config/secrets.ini');

DB\Connection::setCredentials($secrets['db.schema'], $secrets['db.username'], $secrets['db.password']);

session_start();
Application\FrontController::initialize(__DIR__.'/../')
	->handler(new Routing\Manager(__DIR__.'/../config/routes.php'))
	->handler(new Application\Request\Handler\ResponseCache(function() {
		return !isset($_SESSION['user']) || !$_SESSION['user'];
	}))

	->dispatch();
