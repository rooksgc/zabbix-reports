<?php

namespace ZabbixReports;

/**
 * Helper class
 */

defined('APP_EXEC') or die('Restricted access');

class Helper {

	private static $months_ru = array(
		'Январь',
		'Февраль',
		'Март',
		'Апрель',
		'Май',
		'Июнь',
		'Июль',
		'Август',
		'Сентябрь',
		'Октябрь',
		'Ноябрь',
		'Декабрь'
	); 
	
	/**
	 * Получение списка названий месяцев
	 *
	 * @param String  $language
	 * @return Array  $months - массив месяцев
	 */
	public static function getMonths($language = "") {
		if ($language !== "") {
			$array_string = $months_ . $language;
			if (isset(self::$array_string)) {
				return self::$array_string;
			}
		}
		
		return self::$months_ru;
	}
	
	/**
	 * Анализ списка алертов zabbix
	 *
	 * @param  Array $arr
	 * @return Integer $status
	 */
	public static function alertsAnalyse($arr) {
		$status = 0;
		$count = count($arr);
			
		if (empty($arr)) { 			// Нет алертов
			$status = 0;
		} elseif ($count == 1) {  	// Есть 1 алерт
			if ($arr[0][2] == 0) {	// Какой?
				$status = 0;							// Штатно(0)
			} else {
				$status = 2;							// Не 0 - Авария(2)
			}
		} elseif ($count == 2) {	// Есть 2 алерта
			if ($arr[0][2] == 0 && $arr[1][2] != 0) { 						// Первый - доступен, Второй - недоступен
				if ( ($day_end_time - $arr[1][0]) > (int)ANALYZER_WARNING_INTERVAL ) {				// Авария(2)
					$status = 2;
				} else {
					$status = 1;						// Нештатно(1)
				}
			} elseif ($arr[0][2] != 0 && $arr[1][2] == 0) {					// Первый - недоступен, Второй - доступен. Можно вычислить интервал
				if ( ($arr[1][0] - $arr[0][0]) > (int)ANALYZER_WARNING_INTERVAL ) {					// Интервал > 1ч + 1мин. Авария(2)
					$status = 2;
				} else {
					$status = 1; 						// Нештатно(1)
				}
			} else {									// Оба недоступны - Авария(0)
				$status = 2;
			}
		} elseif ($count > 2) {   	// Есть более 2-x алертов - признак возможной Нештатной ситуации(1)
			for ($j = 1; $j < $count; ++$j) {
				if ($arr[$j][2] != 0 && $arr[$j - 1][2] != 0) { 			// 2 последовательных алерта о недоступности - точно Авария(2)
					$status = 2; break;
				} elseif ($arr[$j][2] != 0 && $arr[$j - 1][2] == 0) { 		// Сначала алерт о доступности, потом о недоступности - продолжаем поиск
					continue;
				} elseif ($arr[$j][2] == 0 && $arr[$j - 1][2] != 0) {		// Сначала алерт о недоступности, потом о доступности. Можем вычислить интервал
					if ( ($arr[$j][0] - $arr[$j - 1][0]) > (int)ANALYZER_WARNING_INTERVAL) {			// Интервал > 1ч + 1мин. Авария(2)
						$status = 2; break;
					} else {
						$status = 1; 					// Нештатно(1)
						continue;						// Прододжаем искать последовательности
					}
				}
			}
		}
		
		return $status;
	}
	
}
