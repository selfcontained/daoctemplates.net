<?php
namespace Model;

class Template {

	public $id, $description, $level, $created;

	public $userId, $userName;

	public $className, $classNameShort;

	public $serverName, $realmName;

	public $text;

	public $serverTypeId, $classId;

	public $canEdit = false;

	private $errors = array();

	public function isValid() {

		// user
		if(!$this->userId) {
			$this->errors[] = 'No user attached to template';
		}

		// class
		if(!array_key_exists($this->classId, GameClass::getOptions())) {
			$this->errors[] = 'Invalid class';
		}

		// server type
		if(!array_key_exists($this->serverTypeId, ServerType::getOptions())) {
			$this->errors[] = 'Invalid server type';
		}

		// level
		if(!is_numeric($this->level) || $this->level < 1 || $this->level > 50) {
			$this->errors[] = 'Invalid level';
		}

		// description
		if(!$this->description) {
			$this->errors[] = 'Please enter a description';
		}

		// build report
		if(!$this->text) {
			$this->errors[] = 'Please enter a build report';
		}

		return count($this->errors) === 0;
	}

	public function getErrors() {
		return $this->errors;
	}

}

class TemplateMapper {

	const PAGE_SIZE = 10;

	public static function fetch($id) {
		$sql = 'SELECT 
				templates.TemplateID AS id, 
				templates.Description as description,
				templates.TemplateText as text,
				templates.Level as level,
				templates.UserId AS userId,
				templates.DateSubmitted AS created,
				users.UserName AS userName,
				classes.ClassID AS classId,
				classes.ClassName AS className,
				classes.ClassNameShort AS classNameShort,
				servertypes.ServerTypeID AS serverTypeId,
				servertypes.ServerTypeName AS serverName,
				realms.RealmName AS realmName
			FROM templates
			LEFT JOIN users ON templates.UserID = users.UserID
			LEFT JOIN classes ON templates.ClassID = classes.ClassID
			LEFT JOIN realms ON classes.RealmID = realms.RealmID
			LEFT JOIN servertypes ON templates.ServerTypeID = servertypes.ServerTypeID
			WHERE templates.TemplateID = :id
		';
		$statement = \DB\Connection::getPdo()->prepare($sql);
		$statement->bindParam(':id', $id, \PDO::PARAM_INT);
		$statement->execute();
		$template = $statement->fetchObject('\Model\Template');
		return $template;
	}

	public static function fetchAll($params) {
		$page = isset($params['page']) ? $params['page'] : 1;

		$sql = 'SELECT SQL_CALC_FOUND_ROWS
			templates.TemplateID AS id, 
			templates.Description as description,
			templates.TemplateText as text,
			templates.Level as level,
			templates.UserId AS userId,
			templates.DateSubmitted AS created,
			users.UserName AS userName,
			classes.ClassName AS className,
			classes.ClassNameShort AS classNameShort,
			servertypes.ServerTypeName as serverName,
			realms.RealmName AS realmName';
		if(isset($params['query'])) {
			$sql .= ', MATCH(templates.UserName,templates.ClassName,templates.Description,templates.TemplateText) AGAINST(:query IN BOOLEAN MODE) as totalScore';
		}
		$sql .= '
			FROM templates
			LEFT JOIN users ON templates.UserID = users.UserID
			LEFT JOIN classes ON templates.ClassID = classes.ClassID
			LEFT JOIN realms ON classes.RealmID = realms.RealmID
			LEFT JOIN servertypes ON templates.ServerTypeID = servertypes.ServerTypeID
			WHERE 1=1';

		if(isset($params['query'])) {
			$sql .= ' AND MATCH(templates.UserName,templates.ClassName,templates.Description,templates.TemplateText) AGAINST(:query IN BOOLEAN MODE)';
		}
		
		if(isset($params['serverType'])) {
			$sql .= ' AND servertypes.ServerTypeName = :serverType';
		}

		if(isset($params['className'])) {
			$sql .= ' AND classes.ClassName = :className';
		}

		if(isset($params['userId'])) {
			$sql .= ' AND users.UserID = :userId';
		}
		

		$orderBy = isset($params['query']) ? 'totalScore' : 'DateSubmitted';
		$sql .= ' ORDER BY '.$orderBy.' DESC LIMIT :pageStart,:pageSize';

		$statement = \DB\Connection::getPdo()->prepare($sql);

		$startIndex = ($page - 1) * self::PAGE_SIZE;
		$size = self::PAGE_SIZE;
		$statement->bindParam(':pageStart', $startIndex, \PDO::PARAM_INT);
		$statement->bindParam(':pageSize', $size, \PDO::PARAM_INT);

		if(isset($params['query'])) {
			$statement->bindParam(':query', $params['query']);
		}

		if(isset($params['serverType'])) {
			$statement->bindParam(':serverType', $params['serverType']);
		}
		if(isset($params['className'])) {
			$statement->bindParam(':className', $params['className']);
		}
		if(isset($params['userId'])) {
			$statement->bindParam(':userId', $params['userId'], \PDO::PARAM_INT);
		}

		$statement->execute();
		$templates = $statement->fetchAll(\PDO::FETCH_CLASS, '\Model\Template');
		
		$totalCount = \DB\Connection::getPdo()->query('SELECT FOUND_ROWS();')->fetch(\PDO::FETCH_COLUMN);
		
		return array(
			'page' => $page,
			'totalPages' => ceil($totalCount / $size),
			'total' => $totalCount,
			'currentStart' => $startIndex + 1,
			'currentEnd' => $startIndex + count($templates),
			'pageSize' => $size,
			'data' => $templates
		);
	}

	public static function save(Template $template) {
		if(!$template->id) {
			$sql = 'INSERT INTO templates
				(UserID,UserName,ClassID,ClassName,ServerTypeID,Level,Description,TemplateText,DateSubmitted)
				VALUES (:userId,:userName,:classId,:className,:serverTypeId,:level,:description,:text,NOW())
			';
		}else {
			$sql = 'UPDATE templates SET
				UserID = :userId,
				UserName = :userName,
				ClassID = :classId,
				ClassName = :className,
				ServerTypeID = :serverTypeId,
				Level = :level,
				Description = :description,
				TemplateText = :text
				WHERE templates.TemplateID = :templateId
			';
		}
		
		$statement = \DB\Connection::getPdo()->prepare($sql);
		if($template->id) {
			$statement->bindParam(':templateId', $template->id, \PDO::PARAM_INT);
		}
		$statement->bindParam(':userId', $template->userId, \PDO::PARAM_INT);
		$statement->bindParam(':userName', $template->userName);
		$statement->bindParam(':classId', $template->classId, \PDO::PARAM_INT);
		$classes = \Model\GameClass::getOptions();
		$statement->bindParam(':className', $classes[$template->classId]);
		$statement->bindParam(':serverTypeId', $template->serverTypeId, \PDO::PARAM_INT);
		$statement->bindParam(':level', $template->level, \PDO::PARAM_INT);
		$statement->bindParam(':description', $template->description);
		$statement->bindParam(':text', $template->text);
		
		$saved = $statement->execute();
		// $saved = $statement->rowCount();
		if($saved > 0) {
			if(!$template->id) {
				$template->id = \DB\Connection::getPdo()->lastInsertId();
			}
			return true;
		}else {
			return false;
		}
	}

	public static function delete(Template $template) {
		$sql = 'DELETE FROM templates 
			WHERE templates.TemplateID = :templateId
		';

		$statement = \DB\Connection::getPdo()->prepare($sql);
		$statement->bindParam(':templateId', $template->id, \PDO::PARAM_INT);
		$statement->execute();
		$deleted = $statement->rowCount();
		return $deleted;
	}

}