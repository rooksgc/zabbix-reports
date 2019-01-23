<?php

namespace ZabbixReports;

/**
 * PgDatabase
 */

defined('APP_EXEC') or die('Restricted access');

class PgDatabase {

	 private static $host = ZABBIX_DATABASE_HOST;
	 private static $port = ZABBIX_DATABASE_PORT;
	 private static $name = ZABBIX_DATABASE_NAME;
	 private static $user = ZABBIX_DATABASE_USER;
	 private static $pass = ZABBIX_DATABASE_PASSWORD;

	/**
	 * Установка соединения с базой данных
	 *
	 * @return Object экземпляр соединения с базой данных
	 */
	public static function connect() {
		return pg_connect("
			host=" . self::$host . "
			port=" . self::$port . "
			dbname=" . self::$name . "
			user=" . self::$user . "
			password=" . self::$pass
		);

		// Debug
		// return pg_connection_status($db) == PGSQL_CONNECTION_OK ? $db : die('Database connection failed');
	}
	
	/**
	 * Закрывает соединение с базой данных
	 *
	 * @return Void
	 */
	public static function disconnect() {
		pg_close();
	}
	
	/**
	 * Соединение с базой, выполнение запроса и дисконнект
	 *
	 * @param String $query_string
	 * @param String $fetch
	 * @return Array if $fetch param has been set, Object otherwise
	 */
	public static function query($query_string, $fetch = "") {
		$connect = self::connect();
		$query = pg_query($connect, $query_string);
		
		self::disconnect();

		if ($fetch !== "") {
			if ($fetch == "fetch_row") {
				return self::fetch_row($query);
			}
		}

		return $query;
	}

	/**
	 * Получение данных из запроса в виде массива
	 *
	 * @param Object $query
	 * @return Array $data
	 */
	private static function fetch_row($query) {
		$data = array();
		while ($row = pg_fetch_row($query)) {
			$data[] = $row;
		}
		
		return $data;
	}

}
