<?php
namespace Controller;

require_once('external/recaptcha.php');

class Template extends Base{

	public function view($params) {
		$template = \Model\TemplateMapper::fetch($params['id']);
		if(!$template || !$template->id) {
			return header('Location: /');
		}

		$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
		if($user && $user->id == $template->userId) {
			$template->canEdit = true;
		}

		$this->view->set('template', $template);

		$this->view->display('template/view.twig');
	}

	public function add($params) {
		$this->view->set('captcha', $this->getCaptcha());
		$this->view->set('classes', \Model\GameClass::getOptions());
		$this->view->set('serverTypes', \Model\ServerType::getOptions());
		$this->view->display('template/add.twig');
	}

	public function save($params) {
		$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

		if(!$user) {
			return header('Location: /');
		}

		$templateId = $this->param('templateId');
		if($templateId) {
			$template = \Model\TemplateMapper::fetch($templateId);
			if($template->userId != $user->id) {
				return header('Location: /');
			}
		}else {
			$template = new \Model\Template();
		}

		$template->userId = $user->id;
		$template->userName = $user->name;
		$template->classId = $this->param('class');
		$template->serverTypeId = $this->param('serverType');
		$template->level = $this->param('level');
		$template->description = $this->param('description');
		$template->text = $this->param('text');

		$this->view->set('captcha', $this->getCaptcha());
		$this->view->set('template', $template);
		$this->view->set('classes', \Model\GameClass::getOptions());
		$this->view->set('serverTypes', \Model\ServerType::getOptions());

		if(!$template || !$template->id) {
			// Recaptcha
			$captcha = recaptcha_check_answer(
				'6LcJztYSAAAAADEeqdZFc8r3ZRFqciaJeE98-uIL',
				$_SERVER["REMOTE_ADDR"],
				$this->param("recaptcha_challenge_field"),
				$this->param("recaptcha_response_field")
			);
			$validCaptcha = $captcha->is_valid;
		}else {
			$validCaptcha = true; // no captcha on updates
		}

		if($template->isValid() && $validCaptcha) {
			$created = \Model\TemplateMapper::save($template);

			if($created) {
				// clear response cache for related pages
				\Application\Request\Handler\ResponseCache::expirePages(array(
					'/template/'.$template->id,
					'/user/'.$user->id
				));

				return header('Location: /template/'.$template->id);
			}else {
				$this->view->set('error', 'something went kaboom, sry');
				return $this->view->display('template/add.twig');
			}
		}else {
			$errors = $template->getErrors();
			if(!$validCaptcha) {
				$errors[] = 'Recaptcha is incorrect';
			}
			$errors = implode('<br>', $errors);
			$this->view->set('error', $errors);
			return $this->view->display('template/add.twig');
		}

	}

	public function edit($params) {
		$template = \Model\TemplateMapper::fetch($params['id']);
		if(!$template || !$template->id) {
			return header('Location: /', true, 400);
		}

		$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
		if(!$user || $user->id != $template->userId) {
			return header('Location: /');
		}

		$this->view->set('template', $template);
		$this->view->set('classes', \Model\GameClass::getOptions());
		$this->view->set('serverTypes', \Model\ServerType::getOptions());
		$this->view->display('template/edit.twig');
	}

	public function delete($params) {
		$template = \Model\TemplateMapper::fetch($params['id']);
		if(!$template || !$template->id) {
			return header('Location: /', true, 400);
		}

		$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
		if(!$user || $user->id != $template->userId) {
			return header('Location: /');
		}

		\Model\TemplateMapper::delete($template);

		header('Location: /user/'.$user->id);
	}

	private function getCaptcha() {
		return recaptcha_get_html('6LcJztYSAAAAAHV1sZkiABD66wX3kaMpgr_5Vl15');
	}

}