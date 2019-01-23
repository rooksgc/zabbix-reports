<?php
/**
 * API v1
 * Logical controls
 */

require_once('../../../conf/defines.php');
require_once('../auth.php');

$entityBody = @file_get_contents('php://input');
$json = json_decode($entityBody, true);

define('APP_EXEC', 1);
require_once('../../../app/classes/Helper.php');
require_once('../../../app/classes/PostgresDb.php');

$months = \ZabbixReports\Helper::getMonths('ru');
$sec_in_day = 86400;
$dateNow = date('d', time());
$monthNow = date('m', time());
$yearNow = date('Y', time());
$data = $dBegin = $dEnd = array();

$dBegin[0] = 1;
$dBegin[1] = $dEnd[1] = $monthNow;
$dBegin[2] = $dEnd[2] = $yearNow;
$dEnd[0] = $dateNow;

$days_in_month = cal_days_in_month(CAL_GREGORIAN, $monthNow, $yearNow);

$data[$yearNow . '-' . (+$monthNow)] = array(
	'month' => +$monthNow,
	'year' => +$yearNow,
	'days_count' => $days_in_month
);

$time_start = mktime(0, 0, 0, $monthNow, 1, $yearNow);
$time_end = time();

if ( isset($json['period']['from']) && isset($json['period']['to']) ) {
    $from = $json['period']['from'];
	$to = $json['period']['to'];
	
	// 2018-12-01
	// 0    1  2
	$dBegin = explode('-', $from);
    $dEnd = explode('-', $to);

	$dateBegin = new DateTime($from);
	$dateEnd = new DateTime($to);

	$dateEnd->modify('+1 day');
	$interval = new DateInterval('P1D');
	$daterange = new DatePeriod($dateBegin, $interval ,$dateEnd);

	if ($dateBegin <= $dateEnd) {
        unset($data);
        
		foreach ($daterange as $date) {
			$mf = $date->format("m");

			$data[$date->format("Y") . '-' . $mf] = array(
				'month' => $date->format("m"),
				'year' => $date->format("Y"),
				'days_count' => cal_days_in_month (CAL_GREGORIAN, $date->format("m"), $date->format("Y"))
			);
		}
		
		$time_start = $from;
		$time_end = $to;

		define('PERIOD_DEFINED', 1);
	}
}

// database init
$db = new \SeinopSys\PostgresDb(
	REPORTS_DATABASE_NAME,
	REPORTS_DATABASE_HOST,
	REPORTS_DATABASE_USER,
	REPORTS_DATABASE_PASSWORD
);

$db->getConnection();

if (!$db->tableExists('logical_controls')) {
	header('Content-type: application/json');
	echo json_encode(array(
        "status"  => "error",
        "message" => "Таблицы logical_controls не существует"
    ));
    exit();
}

$rows_ = $db->query('SELECT DISTINCT entity_name AS name FROM logical_controls ORDER BY entity_name ASC');

$rows = array();
foreach($rows_ as $row) {
	$rows[] = $row['name'];
}
unset($rows_);

$statuses = $db->query("SELECT entity_name AS name, status, date FROM logical_controls WHERE date BETWEEN ? AND ? ORDER BY entity_name ASC", [$time_start, $time_end]);

$dayStart = defined('PERIOD_DEFINED') ? $dBegin[2] : 1;
$dayEnd = defined('PERIOD_DEFINED') ? $dEnd[2] : $dateNow;

if (isset($data)) {
	$headersSet = false;

	// Цикл по месяцам
	// $key = 2019-01
	foreach ($data as $key => $month) {
		$daysOffset = 0;

		$data[$key]['headers'] = array();
		$data[$key]['rows'] = array();
		
		// Цикл по ресурсам (rows) (сайты и т.п.)
		$res = 0;

		foreach ($rows as $title) {
			$data[$key]['rows'][$res] = array();
			
			array_push($data[$key]['rows'][$res], array(
				"value" => false,
				'text' => $title
			));

			// Первый заголовок - Ресурсы
			if ($res === 0) {
				array_push($data[$key]['headers'], array(
					"text" => "Ресурс",
					"align" => "left",
					"sortable" => true,
					"value" => "name",
					"width" => 200
				));
			}

			// Цикл по дням в месяце от 1 до последнего
			for ($j = 0; $j < $month['days_count']; ++$j) {
				// Пропуск дня по условию
				if ( ($month['month'] == $dBegin[1] && $j + 1 < $dayStart)
					|| ($month['month'] == $dEnd[1] && $j + 1 > $dayEnd)
				) {
					++$daysOffset;
					continue;
				}

				// Наполняем заголовки
				if (!$headersSet && $res === 0) {
					array_push($data[$key]['headers'], array(
						"value" => $j + 1,
						"text"  => $j + 1,
						"sortable" => false
					));

					if ($j == (int)$month['days_count']) {
						$headersSet = true;
					}
				}


				$status = 0;

				// Ищем статус за день $stat['date'], например 2019-01-21
				foreach ($statuses as $stat) {
					// Проверяем имя ресурса $title
					if (strpos($stat['name'], $title) === FALSE) continue;
					// Проверяем дату в entity
					$testableDate = $month['year'] . '-' . $month['month'] . '-' . ($j + 1);
					if ($testableDate == $stat['date']) {
						$status = $stat['status'];
					}
				}

				// res[1...n] - данные по дням
				array_push($data[$key]['rows'][$res], array(
					"value" => false,
					"text" => $status
				));
			}
			$res++;
		}
    }
}

header('Content-type: application/json');
echo json_encode(array(
	'months' => $data
), JSON_UNESCAPED_UNICODE);
exit();

?>
