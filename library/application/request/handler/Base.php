<?php

namespace Application\Request\Handler;

abstract class Base {

	public function __construct() {

	}

	public function init() {}

	public function preExecute() {}

	public function execute() {}

	public function postExecute() {}

}
