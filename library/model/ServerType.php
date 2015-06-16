<?php
namespace Model;

class ServerType {

	private static $options = array(
		1 => 'ToA',
		2 => 'Classic',
		3 => 'PvP',
		4 => 'Coop'
	);
	
	public static function getOptions() {
		return self::$options;
	}

}