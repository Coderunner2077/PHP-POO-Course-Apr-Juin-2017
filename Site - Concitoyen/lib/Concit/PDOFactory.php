<?php
namespace Concit;

class PDOFactory {
	public static function getMysqlConnexion() {
		$db = new \PDO('mysql:host=localhost;dbname=mybdd', 'root', 'root');
		$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		return $db;
	}
}