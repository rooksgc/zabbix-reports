<?php

namespace ZabbixReports;

/**
 * Класс авторизации
 */
 
defined('APP_EXEC') or die('Restricted access');

class Authorization {

	private static $success_text = AUTH_SUCCESS_TEXT;
	private static $wrong_pass = AUTH_WRONG_PASS;
	private static $wrong_captcha = AUTH_WRONG_CAPTCHA;
	private static $login_template_path = AUTH_LOGIN_TEMPLATE_PATH;
	private static $logout_template_path = AUTH_LOGOUT_TEMPLATE_PATH;
	private static $keys = array(array('hash' => AUTH_HASH1, 'secret' => AUTH_SECRET));
	
	/**
	 * Аутентификация пользователя
	 *
	 * @return Void
	 */
	public static function login() {
		session_start();
			
		if (isset($_POST['logout'])) {
			if (isset($_SESSION['pass'])) {
				self::doLogout();
			}
			self::loginStatus(false);
		}

		if (self::checkToken($_POST['token']) && $_POST['pass'] && $_POST['captcha']) {
			unset($_SESSION['token']);
			if (self::checkCaptcha($_POST['captcha']) ) {
				unset($_SESSION['captcha']);
				if (self::checkLogin($_POST['pass'], false)) {
					self::doLogin($_POST['pass']);
					self::loginStatus(true, self::$success_text);
				} else {
					self::loginStatus(false, self::$wrong_pass);
				}
			} else {
				self::loginStatus(false, self::$wrong_captcha);
			}
		} else {
			if ($_SESSION['pass']) {
				if (self::checkLogin($_SESSION['pass'])) {
					self::loginStatus(true, self::$success_text);
				}
			} else {
				self::loginStatus(false);
			}
		}
	}
	
	/**
	 * Проверка токена
	 * 
	 * Сравнивает переданный токен и токен из сессии
	 *
	 * @param  String   $token
	 * @return Boolean
	 */
	private static function checkToken($token) {
		return $token == $_SESSION['token'];
	}

	/**
	 * Проверка кода капчи
	 * 
	 * Сравнивает переданный код капчи и сгенерированный сервером
	 *
	 * @param   string   $captcha
	 * @return  boolean
	 */
	private static function checkCaptcha($captcha) {
		return strtolower($captcha) == $_SESSION['captcha'];
	}

	/**
	 * Проверка пароля пользователя
	 *
	 * @param  String   $pass
	 * @param  Boolean  $isHash
	 * @return Boolean
	 */
	private static function checkLogin($pass, $isHash = true) {
		if (!$isHash) {
			$secret = self::$keys[0]['secret'];
			$pass = sha1(md5($pass) . $secret);
		}
		return ($pass == self::$keys[0]['hash']);
	}

	/**
	 * Аутентифицирует пользователя в системе
	 *
	 * @param  String $pass
	 * @return Void
	 */
	private static function doLogin($pass) {
		self::doLogout();
		$secret = self::$keys[0]['secret'];
		$pass = sha1(md5($pass) . $secret);
		$_SESSION['pass'] = $pass;
		$_SESSION['access'] = true;
	}

	
	/**
	 * Очистка сессии
	 *
	 * @return Void
	 */
	private static function doLogout() {
		unset($_SESSION['pass']);
		unset($_SESSION['access']);
	}

	/**
	 * Показ формы логина/логаута
	 * 
	 * Статус, равный false останавливает приложение
	 *
	 * @param  Boolean $status
	 * @param  String  $message
	 * @return Boolean
	 */	
	private static function loginStatus($status, $message = "") {
		$class = $status ? 'alert alert-success' : 'alert alert-danger';
		$align = $status ? 'right' : 'center';
		echo $message != "" ? "<div class='message-".$align."'><p class='text-center " . $class . "'><span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>&nbsp;" . $message . "</p></div>" : "";

		if ($status) {
			self::displayLogoutForm();
		} else {
			self::displayLoginForm();
			exit();
		}
	}

	/**
	 * Подключение шаблона формы логина
	 *
	 * @return Void
	 * @todo Fix require_once
	 */
	private static function displayLoginForm() {
		require_once(self::$login_template_path);
	}

	/**
	 * Подключение шаблона формы логаута
	 *
	 * @return Void
	 * @todo Fix require_once
	 */
	private static function displayLogoutForm() {
		require_once(self::$logout_template_path);
	}
	
}
