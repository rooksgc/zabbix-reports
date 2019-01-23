<?php
/**
 * API v1
 * Rubricators
 */

require_once('../../../conf/defines.php');
require_once('../auth.php');

$entityBody = @file_get_contents('php://input');
$json = json_decode($entityBody, true);

define('APP_EXEC', 1);
require_once('../../../app/classes/Helper.php');
require_once('../../../app/classes/Database.php');

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
			$month_num = (+$mf < 10) ? '0' . $mf : $mf;

			$data[$date->format("Y") . '-' . $mf] = array(
				'month' => +$date->format("m"),
				'year' => $date->format("Y"),
				'days_count' => cal_days_in_month (CAL_GREGORIAN, $date->format("m"), $date->format("Y"))
			);
		}
		
		$time_start = mktime(0, 0, 0, $dBegin[1], $dBegin[2], $dBegin[0]);
		$hoursEndDay = 23;
		$minutesEndDay = 59;

		if ($dEnd[2] == $dateNow) {
			$hoursEndDay = date('H', time());
			$minutesEndDay = date('i', time());
		}

		$time_end = mktime ($hoursEndDay, $minutesEndDay, 59, $dEnd[1], $dEnd[2], $dEnd[0]);

		define('PERIOD_DEFINED', 1);
	}
}

$rows = \ZabbixReports\PgDatabase::query(<<<DB
	SELECT DISTINCT h.name, s.url FROM httptest AS h
	LEFT JOIN httpstep AS s ON h.httptestid = s.httptestid
	LEFT JOIN hosts_groups AS g ON h.hostid = g.hostid
	WHERE g.groupid = 9 AND h.name ILIKE '%рубрикатор%'
	ORDER BY h.name ASC
DB
	, "fetch_row");

$alerts = \ZabbixReports\PgDatabase::query(<<<DB
	SELECT clock, subject, esc_step FROM alerts
	WHERE clock BETWEEN {$time_start} AND {$time_end}
	ORDER BY clock ASC
DB
	, "fetch_row");

$dayStart = defined('PERIOD_DEFINED') ? $dBegin[2] : 1;
$dayEnd = defined('PERIOD_DEFINED') ? $dEnd[2] : $dateNow;

if (isset($data)) {
	$headersSet = false;

	foreach ($data as $key => $month) {
		$daysOffset = 0;

		$data[$key]['headers'] = array();
		$data[$key]['rows'] = array();
		
		$res = 0;

		foreach ($rows as $entry) {
			$data[$key]['rows'][$res] = array();

			$link = $entry[1];
			$title = $entry[0];
			
			array_push($data[$key]['rows'][$res], array(
				"value" => false,
				'text' => "<a href='".$link."' target='_blank'>".$title."</a>"
			));

			if ($res === 0) {
				array_push($data[$key]['headers'], array(
					"text" => "Ресурс",
					"align" => "left",
					"sortable" => true,
					"value" => "name",
					"width" => 200
				));
			}

			for ($j = 0; $j < $month['days_count']; ++$j) {
				if ( ($month['month'] == $dBegin[1] && $j + 1 < $dayStart)
					|| ($month['month'] == $dEnd[1] && $j + 1 > $dayEnd)
				) {
					++$daysOffset;
					continue;
				}

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

				$arr = array();
				$day_start_time = mktime(0, 0, 0, $month['month'], $j + 1, $month['year']);
				$day_end_time = $sec_in_day + $day_start_time;

				$status = 0;

				// Ищем алерты за этот день и складываем в массив
				foreach ($alerts as $alert) {
					if ($alert[0] < $day_start_time) continue;
					if ($alert[0] >= $day_end_time) continue;
					// Алерт только для имени указанного ресурса $entry[0] - имя ресурса
					if (strpos($alert[1], $entry[0]) === FALSE) continue;
					if ($month['month'] != +date('m', $alert[0])) continue;
					$arr[] = $alert;
				}

				$status = \ZabbixReports\Helper::alertsAnalyse($arr);
				unset($arr);

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
