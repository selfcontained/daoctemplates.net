<?php

namespace Application\Request\Handler;

class ResponseCache extends Base {

	const CACHE_NAMESPACE = 'RESPONSE_CACHE';

	protected $bufferOutput = false;

	protected $cacheTTL = 3600; //1 hour

	protected $cacheKey;

	protected $test;

	public function __construct($test) {
		$this->test = $test;
	}

	/**
	 * Before execute occurs, determine if we're caching the output
	 * 	- responseCache route attribute is true
	 * 	- responseCacheTTL route attribute will override default output cache TTL
	 * Deliver cached output if it exists, or start an output buffer if it doesn't
	 */
	public function preExecute() {
		$frontController = \Application\FrontController::getInstance();
		$route = $frontController->data['route'];
		if($route->getAttribute('responseCache') === true) {
			if($this->test && call_user_func($this->test)) {

				$this->cacheKey = $this->getCacheKey($route->getPattern());
				$this->cacheTTL = $route->getAttribute('responseCacheTTL') ?: $this->cacheTTL;

				$output = $this->fetch();
				if($output === false) {
					$this->bufferOutput = true;
					ob_start();
				}else {
					$this->flushResponse($output);
				}

			}

		}
	}

	/**
	 * After execution get output buffer contents if we are buffering, store it in cache, and output it
	 */
	public function postExecute() {
		if($this->bufferOutput) {
			$output = ob_get_contents();
			ob_end_clean();
			$this->store($output);
			$this->flushResponse($output);
		}
	}

	protected function flushResponse($output) {
		echo $output;
		die();
	}

	protected function fetch() {
		return apc_fetch($this->cacheKey);
	}

	protected function store($output) {
		apc_store($this->cacheKey, $output, $this->cacheTTL);
	}

	protected function getCacheKey($routePattern) {
		$key = $_SERVER['REQUEST_URI'];
		return self::CACHE_NAMESPACE . ':' . $key;
	}

	public static function expirePages($pages) {
		if(!is_array($pages)) $pages = array($pages);
		foreach($pages AS $page) {
			apc_delete(self::CACHE_NAMESPACE . ':' . $page);
		}
	}

}
