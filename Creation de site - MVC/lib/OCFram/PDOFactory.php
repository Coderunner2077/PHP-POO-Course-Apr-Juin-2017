<?php
namespace OCFram;

class PDOFactory {
	public static function getMysqlConnexion() {
		$dao = new \PDO('mysql:host=news;dbname=mybdd', 'root', 'root');
		$dao->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		return $dao;
	}
}