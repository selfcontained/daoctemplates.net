<?php
namespace Controller;

class User extends Base {

	public function view($params) {
		if($this->param('query')) {
			header('Location: /?query='.$this->param('query'));
		}

		$id = isset($params['id']) ? $params['id'] : null;
		
		$user = \Model\UserMapper::fetch($id);

		if(!$user) {
			header('Location: /');
		}

		$page = $this->param('page') ?: 1;
		$templates = \Model\TemplateMapper::fetchAll(array(
			'userId' => $user->id,
			'page' => $page
		));

		// add canEdit flag for owners
		$currentUser = isset($_SESSION['user']) ? $_SESSION['user'] : null;
		if($currentUser && $currentUser->id == $user->id) {

			foreach($templates['data'] AS $template) {

				$template->canEdit = true;
			}
		}

		$this->view->set('user', $user);
		$this->view->set('user', $user);
		$this->view->set('templates', $templates);
		$this->view->display('user/view.twig');
	}

}