<?php
namespace Routing\Handler;

class Redirect extends Base{

	private $func;

	public function __construct($func) {
		$this->func = $func;
		return $this;
	}

	public function dispatch($params) {
		$location = call_user_func($this->func);
		header('Location: '.$location, true, 301);
	}

}
