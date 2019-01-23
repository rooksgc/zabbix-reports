<?php
/**
 * API v1
 * Portals
 * Export to xlsx
 */

require_once('../../../../../conf/defines.php');
require_once('../../../auth.php');

define('APP_EXEC', 1);
require_once(APP_PATH . '/app/classes/Helper.php');
require_once(APP_PATH . '/app/classes/Database.php');
require_once(APP_PATH . '/lib/PHPExcel.php');

$objPHPExcel = new PHPExcel();

$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory;
PHPExcel_Settings::setCacheStorageMethod($cacheMethod);

$objPHPExcel->getProperties()->setCreator(EXCEL_META_CREATOR)
	->setLastModifiedBy(EXCEL_META_LAST_MODIFIED_BY)
	->setTitle(EXCEL_META_TITLE)
	->setSubject(EXCEL_META_SUBJECT)
	->setDescription(EXCEL_META_DESCRIPTION)
	->setKeywords(EXCEL_META_KEYWORDS)
	->setCategory(EXCEL_META_CATEGORY);

$column_array = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O",
"P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "AA", "AB", "AC", "AD", "AE", "AF", "AG");

$data_head = array(
	array(NULL, TEXT_STATUS_ON),
	array(NULL, TEXT_STATUS_MID),
	array(NULL, TEXT_STATUS_OFF),
	array(NULL, NULL),
	array(NULL, NULL),
	array(EXCEL_SHEET_0_HEAD_NUMBER, EXCEL_SHEET_0_HEAD_TITLE)
);

$B1B3Style = array(
  'font'  => array(
    'bold'  => true,
    'size'  => 11,
    'name'  => 'Calibri'
  ),
	'fill' => array(
		'type' => PHPExcel_Style_Fill::FILL_SOLID,
	),
	'borders' => array(
		'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	)
);

$B1Style = array(
  'font'  => array(
    'color' => array('rgb' => '006100')
  ),
	'fill' => array(
		'color' => array('rgb' => 'C6EFCE')
	)
);

$B2Style = array(
  'font'  => array(
    'color' => array('rgb' => '9C6500')
  ),
	'fill' => array(
		'color' => array('rgb' => 'FFEB9C')
	)
);

$B3Style = array(
  'font'  => array(
    'color' => array('rgb' => '9C0006')
  ),
	'fill' => array(
		'color' => array('rgb' => 'FFC7CE')
	)
);

$B6Style = array(
  'font'  => array(
		'size'  => 11,
		'bold'  => true,
    'name'  => 'Arial'
  ),
	'alignment' => array(
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
		'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
	),
	'borders' => array(
		'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN
		),
		'diagonal' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN
		),
    'diagonaldirection' => PHPExcel_Style_Borders::DIAGONAL_DOWN
	)
);

$data_cells_style = array(
  'font'  => array(
		'size'  => 11,
    'name'  => 'Arial'
  ),
	'alignment' => array(
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
	),
	'borders' => array(
		'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	)
);

$data_cells_status_style = array(
	'borders' => array(
		'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	)
);

$data_cells_fill0 = array(
	'fill' => array(
		'type' => PHPExcel_Style_Fill::FILL_SOLID,
		'color' => array('rgb' => 'C6EFCE')
	)
);

$data_cells_fill1 = array(
	'fill' => array(
		'type' => PHPExcel_Style_Fill::FILL_SOLID,
		'color' => array('rgb' => 'FFEB9C')
	)
);

$data_cells_fill2 = array(
	'fill' => array(
		'type' => PHPExcel_Style_Fill::FILL_SOLID,
		'color' => array('rgb' => 'FFC7CE')
	)
);

$list_column_style = array(
  'alignment' => array(
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
		'wrap' => true
	)
);

function createComment($objPHPExcel, $cell, $type) {
	switch ($type) {
		case 0:
			$objPHPExcel->getActiveSheet()->getComment($cell)->setWidth(530)->setHeight(105);
			$comment = $objPHPExcel->getActiveSheet()->getComment($cell)->getText();
			$comment->createTextRun(TEXT_STATUS_ON)->getFont()->setSize(12)->setBold(true)->setUnderline(true);
			$comment->createTextRun("\r\n");
			$comment->createTextRun(EXCEL_TEXT_PORTAL_AVAILABILITY_MONITORING);
			$comment->createTextRun(EXCEL_TEXT_PORTAL_AVAILABLE)->getFont()->setBold(true);
			$comment->createTextRun("\r\n");
			$comment->createTextRun(EXCEL_TEXT_PORTAL_PAGES_AND_SERVICES_CONTROL);
			$comment->createTextRun(EXCEL_TEXT_PORTAL_AVAILABLE)->getFont()->setBold(true);
			$comment->createTextRun("\r\n");
			$comment->createTextRun(EXCEL_TEXT_PORTAL_REPORTING_CONTROL);
			$comment->createTextRun(EXCEL_TEXT_PORTAL_AVAILABLE_DATA_CORRECT)->getFont()->setBold(true);
			$comment->createTextRun("\r\n");
			$comment->createTextRun(EXCEL_TEXT_PORTAL_DATASOURCES_CONTROL);
			$comment->createTextRun(EXCEL_TEXT_ACTUAL)->getFont()->setBold(true);
			unset($comment); break;
		case 1:
			$objPHPExcel->getActiveSheet()->getComment($cell)->setWidth(530)->setHeight(85);
			$comment = $objPHPExcel->getActiveSheet()->getComment($cell)->getText();
			$comment->createTextRun(TEXT_STATUS_MID)->getFont()->setSize(12)->setBold(true)->setUnderline(true);
			$comment->createTextRun("\r\n");
			$comment->createTextRun(EXCEL_TEXT_PORTAL_PAGES_AND_SERVICES_CONTROL);
			$comment->createTextRun(EXCEL_TEXT_PORTAL_UNAVAILABLE_ERROR)->getFont()->setBold(true);
			$comment->createTextRun("\r\n");
			$comment->createTextRun(EXCEL_TEXT_PORTAL_REPORTING_CONTROL);
			$comment->createTextRun(EXCEL_TEXT_PORTAL_ERROR_DATA_CONTROL_ERROR)->getFont()->setBold(true);
			$comment->createTextRun("\r\n");
			$comment->createTextRun(EXCEL_TEXT_PORTAL_DATASOURCES_CONTROL);
			$comment->createTextRun(EXCEL_TEXT_NOT_ACTUAL)->getFont()->setBold(true);
			unset($comment); break;
		case 2:
			$objPHPExcel->getActiveSheet()->getComment($cell)->setWidth(530)->setHeight(45);
			$comment = $objPHPExcel->getActiveSheet()->getComment($cell)->getText();
			$comment->createTextRun(TEXT_STATUS_OFF)->getFont()->setSize(12)->setBold(true)->setUnderline(true);
			$comment->createTextRun("\r\n");
			$comment->createTextRun(EXCEL_TEXT_PORTAL_AVAILABILITY_MONITORING);
			$comment->createTextRun(EXCEL_TEXT_PORTAL_UNAVAILABLE)->getFont()->setBold(true);
			unset($comment); break;
	}
}

$months = \ZabbixReports\Helper::getMonths('ru');
$sec_in_day = 86400;
$dateNow = date('d', time());
$monthNow = date('m', time());
$yearNow = date('Y', time());
$monthsAll = $dBegin = $dEnd = array();

$dBegin[0] = 1;
$dBegin[1] = $dEnd[1] = $monthNow;
$dBegin[2] = $dEnd[2] = $yearNow;
$dEnd[0] = $dateNow;

$days_in_month = cal_days_in_month(CAL_GREGORIAN, $monthNow, $yearNow);

$monthsAll[$months[+$monthNow - 1] . " " . $yearNow] = array(
	'month' => +$monthNow,
	'year' => +$yearNow,
	'days_count' => $days_in_month
);

$time_start = mktime(0, 0, 0, $monthNow, 1, $yearNow);
$time_end = time();

if (isset($_GET['period']) && preg_match("/^\d\d\.\d\d\.\d{4}\-\d\d\.\d\d\.\d{4}$/", $_GET['period'])) {

	$dateAll = explode('-', $_GET['period']);
	$dBegin = explode('.', $dateAll[0]);
	$dEnd = explode('.', $dateAll[1]);

	$dateBegin = new DateTime($dateAll[0]);
	$dateEnd = new DateTime($dateAll[1]);
	$dateEnd->modify('+1 day');
	$interval = new DateInterval('P1D');
	$daterange = new DatePeriod($dateBegin, $interval ,$dateEnd);

	if ($dateBegin <= $dateEnd) {
		unset($monthsAll);

		foreach ($daterange as $date) {
			$monthsAll[$months[+$date->format("m") - 1] . " " . $date->format("Y")] = array(
				'month' => +$date->format("m"),
				'year' => $date->format("Y"),
				'days_count' => cal_days_in_month (CAL_GREGORIAN, $date->format("m"), $date->format("Y"))
			);
		}
		
		$time_start = mktime(0, 0, 0, $dBegin[1], $dBegin[0], $dBegin[2]);

		$hoursEndDay = 23;
		$minutesEndDay = 59;

		if ($dEnd[0] == $dateNow) {
			$hoursEndDay = date('H', time());
			$minutesEndDay = date('i', time());
		}

		$time_end = mktime ($hoursEndDay, $minutesEndDay, 59, $dEnd[1], $dEnd[0], $dEnd[2]);

		define('PERIOD_DEFINED', 1);
	}
}

$list = \ZabbixReports\PgDatabase::query(<<<DB
	SELECT DISTINCT h.name, s.url FROM httptest AS h
	LEFT JOIN httpstep AS s ON h.httptestid = s.httptestid
	LEFT JOIN hosts_groups AS g ON h.hostid = g.hostid
	WHERE g.groupid = 9 AND h.name ILIKE '%сайт%'
	ORDER BY h.name ASC
DB
	, "fetch_row");

$alerts = \ZabbixReports\PgDatabase::query(<<<DB
	SELECT clock, subject, esc_step FROM alerts
	WHERE clock BETWEEN {$time_start} AND {$time_end}
	ORDER BY clock ASC
DB
	, "fetch_row");

$month_counter = 0;
$dayStart = defined('PERIOD_DEFINED') ? $dBegin[0] : 1;
$dayEnd = defined('PERIOD_DEFINED') ? $dEnd[0] : $dateNow;

if (isset($monthsAll)) {

	foreach ($monthsAll as $m) {
		$daysOffset = 0;

		if ($month_counter) $objPHPExcel->createSheet($month_counter);

		$objPHPExcel->setActiveSheetIndex($month_counter);
		$objPHPExcel->getActiveSheet()->setTitle($months[$m['month'] - 1] . " " . $m['year']);

		createComment($objPHPExcel, "B1", 0);
		createComment($objPHPExcel, "B2", 1);
		createComment($objPHPExcel, "B3", 2);

		$last_column_index = $m['days_count'] == 29 ? "AE" : ($m['days_count'] == 30 ? "AF" : "AG");

		$objPHPExcel->getActiveSheet()->getStyle('B1:B3')->applyFromArray($B1B3Style);
		$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($B1Style);
		$objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($B2Style);
		$objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray($B3Style);
		$objPHPExcel->getActiveSheet()->getStyle('B6')->applyFromArray($B6Style);
		$objPHPExcel->getActiveSheet()->getStyle("C6:" . $last_column_index . "6")->applyFromArray($data_cells_style);

		for ($i = 6, $c = count($list); $i <= $c + 6; ++$i) {
			$objPHPExcel->getActiveSheet()->getStyle("A" . $i)->applyFromArray($data_cells_style);
			$objPHPExcel->getActiveSheet()->getStyle("B" . $i)->applyFromArray($data_cells_style);
			$objPHPExcel->getActiveSheet()->getStyle("B" . $i)->applyFromArray($list_column_style);
		}

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(58);
		$objPHPExcel->getActiveSheet()->getRowDimension(6)->setRowHeight(47);

		$objPHPExcel->getActiveSheet()->fromArray($data_head, NULL, 'A1'); // start cell

		$data_days = array();
		for ($i = 1; $i <= $m['days_count']; ++$i) {
			$data_days[] = $i;
		}

		$objPHPExcel->getActiveSheet()->fromArray($data_days, NULL, 'C6'); // start cell

		$data_sitenames = $data_indexes = array();
		$index_count = 0;
		$list_row_start = 7;
		$list_column_start = 2;

		for ($l = 0, $c = count($list); $l < $c; ++$l) {

			$data_indexes[] = array(++$index_count);
			$data_sitenames[] = array($list[$l][0]);
			
			for ($j = 0; $j < $m['days_count']; ++$j) {
				
				if ( ($m['month'] == $dBegin[1] && $j + 1 < $dayStart) || ($m['month'] == $dEnd[1] && $j + 1 > $dayEnd) ) {
					++$daysOffset;
					continue;
				}

				$arr = array();

				$day_start_time = mktime(0, 0, 0, $m['month'], $j + 1, $m['year']);
				$day_end_time = $sec_in_day + $day_start_time;

				foreach ($alerts as $alert) {
					if ($alert[0] < $day_start_time) continue;
					if ($alert[0] >= $day_end_time) continue;
					if (strpos($alert[1], $list[$l][0]) === FALSE) continue;
					if ($m['month'] != +date('m', $alert[0])) continue;

					$arr[] = $alert;
				}
				
				$status = \ZabbixReports\Helper::alertsAnalyse($arr);

				unset($arr);

				$row = $list_row_start + $l;
				$col = $list_column_start + $j;

				$objPHPExcel->getActiveSheet()->getStyle($column_array[$col]
					. $row)->applyFromArray($data_cells_status_style);

				switch ($status) {
					case 0:
						$objPHPExcel->getActiveSheet()->getStyle($column_array[$col]
						. $row)->applyFromArray($data_cells_fill0); break;
					case 1:
						$objPHPExcel->getActiveSheet()->getStyle($column_array[$col]
						. $row)->applyFromArray($data_cells_fill1); break;
					case 2:
						$objPHPExcel->getActiveSheet()->getStyle($column_array[$col]
						. $row)->applyFromArray($data_cells_fill2); break;
				}

				createComment($objPHPExcel, $column_array[$col] . $row, $status);
			}
		}

		$objPHPExcel->getActiveSheet()->fromArray($data_indexes, NULL, 'A7'); // start cell
		$objPHPExcel->getActiveSheet()->fromArray($data_sitenames, NULL, 'B7'); // start cell

		++$month_counter;
	}
}

$monthFormat = $month < 10 ? "0" . $month : $month;
$fileDate = defined('PERIOD_DEFINED') ? $_GET['period'] : date("d-m-Y", time());
$filename = "portals-" . $fileDate;

header ("Expires: Mon, 1 Jan 1970 00:00:00 GMT");
header ("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header ("Content-type: application/vnd.ms-excel");
header ("Content-Disposition: attachment; filename=" . $filename . ".xlsx");

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
