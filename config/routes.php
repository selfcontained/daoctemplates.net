<?php
return array(
	'/^\/$/' => array(
		'type' => 'Controller',
		'args' => array('Controller\Home', 'index'),
		'attributes' => array( 'responseCache'=>true, 'responseCacheTTL'=>900 )
	),
	'/^\/(?<type>toa|classic|coop|pvp)$/' => array(
		'type' => 'Controller',
		'args' => array('Controller\Home', 'index')
	),
	'/^\/template\/(?<id>[0-9]+)$/' => array(
		'type' => 'Controller',
		'args' => array('Controller\Template', 'view'),
		'attributes' => array( 'responseCache'=>true )
	),
	'/^\/user\/(?<id>[0-9]+)$/' => array(
		'type' => 'Controller',
		'args' => array('Controller\User', 'view'),
		'attributes' => array( 'responseCache'=>true )
	),
	'/^\/class\/(?<class>[a-z\-]+)(\/)?((?<type>toa|classic|coop|pvp))?$/' => array(
		'type' => 'Controller',
		'args' => array('Controller\Home', 'classes'),
		'attributes' => array( 'responseCache'=>true, 'responseCacheTTL'=>900 )
	),
	'/^\/template\/add$/' => array(
		'type' => 'Controller',
		'args' => array('Controller\Template', 'add')
	),
	'/^\/template\/edit\/(?<id>[0-9]+)$/' => array(
		'type' => 'Controller',
		'args' => array('Controller\Template', 'edit')
	),
	'/^\/template\/save$/' => array(
		'type' => 'Controller',
		'args' => array('Controller\Template', 'save')
	),
	'/^\/template\/delete\/(?<id>[0-9]+)$/' => array(
		'type' => 'Controller',
		'args' => array('Controller\Template', 'delete')
	),
	'/^\/account$/' => array(
		'type' => 'Controller',
		'args' => array('Controller\Account', 'edit')
	),
	'/^\/account\/new$/' => array(
		'type' => 'View',
		'args' => array('account/new.twig')
	),
	'/^\/account\/create$/' => array(
		'type' => 'Controller',
		'args' => array('Controller\Account', 'create')
	),
	'/^\/account\/save$/' => array(
		'type' => 'Controller',
		'args' => array('Controller\Account', 'save')
	),
	'/^\/login$/' => array(
		'type' => 'Controller',
		'args' => array('Controller\Account', 'login')
	),
	'/^\/logout$/' => array(
		'type' => 'Controller',
		'args' => array('Controller\Account', 'logout')
	),
	'/^\/css\/combined\.(?<bundle>[a-z]+)\.css$/' => array(
		'type' => 'Less'
	),
	'/^\/about$/' => array(
		'type' => 'Controller',
		'args' => array('Controller\Home', 'about'),
		'attributes' => array( 'responseCache'=>true )
	),
	// redirects for old site
	// templates
	'/^\/viewTemplate\.php$/' => array(
		'type' => 'Redirect',
		'args' => array(function() {
			return '/template/'.$_REQUEST['template'];
		})
	),
	// class listings
	'/^\/classListing\.php$/' => array(
		'type' => 'Redirect',
		'args' => array(function() {
			$classId = isset($_REQUEST['ClassID']) ? $_REQUEST['ClassID'] : null;
			$classes = \Model\GameClass::getOptions();
			if($classId && isset($classes[$classId])){
				return '/class/'.strtolower($classes[$classId]);
			}else {
				return '/';
			}
		})
	),
	// server types on homepage
	'/^\/index.php$/' => array(
		'type' => 'Redirect',
		'args' => array(function() {
			$serverTypeId = isset($_REQUEST['ServerTypeID']) ? $_REQUEST['ServerTypeID'] : null;
			$types = \Model\ServerType::getOptions();
			if($serverTypeId && isset($types[$serverTypeId])){
				return '/'.strtolower($types[$serverTypeId]);
			}else {
				return '/';
			}
		})
	),
	// server types on homepage
	'/^\/viewUser.php$/' => array(
		'type' => 'Redirect',
		'args' => array(function() {
			$userId = isset($_REQUEST['UserID']) ? $_REQUEST['UserID'] : null;
			if($userId){
				return '/user/'.$userId;
			}else {
				return '/';
			}
		})
	),
	// server types on homepage
	'/^\/(forums|viewThread).php$/' => array(
		'type' => 'Redirect',
		'args' => array(function() {
			return '/';
		})
	)
);
