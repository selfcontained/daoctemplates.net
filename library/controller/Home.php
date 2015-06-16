<?php
namespace Controller;

class Home extends Base{

	public function index($params) {

		$type = isset($params['type']) ? $params['type'] : null;
		$page = $this->param('page') ?: 1;
		$query = $this->param('query');

		$templates = \Model\TemplateMapper::fetchAll(array(
			'query' => $query,
			'serverType' => $type,
			'page' => $page
		));

		$this->view->set('templates', $templates);
		$this->view->set('serverType', $type?:'all');
		$this->view->set('query', $query);
		if($type != 'all') {
			$this->view->set('searchRoot', '/'.$type);
		}

		if($this->param('login') == 'epicfail') {
			$this->view->set('error', 'Invalid username/password');
		}

		$this->view->display('home/index.twig');
	}

	public function classes($params) {

		$class = isset($params['class']) ? $params['class'] : null;
		$type = isset($params['type']) ? $params['type'] : null;
		$page = $this->param('page') ?: 1;
		$query = $this->param('query');

		$templates = \Model\TemplateMapper::fetchAll(array(
			'className' => $class,
			'serverType' => $type,
			'page' => $page,
			'query' => $query
		));

		$this->view->set('templates', $templates);
		$this->view->set('serverType', $type?:'all');
		$this->view->set('class', $class);
		$this->view->set('query', $query);

		$this->view->display('home/class.twig');
	}

	public function about($params) {
		$this->view->display('home/about.twig');	
	}

}