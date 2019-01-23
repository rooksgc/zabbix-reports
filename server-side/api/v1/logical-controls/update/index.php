<?php
/**
 * API v1
 * Logical Controls
 * 
 * PostgresDb class documentation
 * @see https://github.com/SeinopSys/PHP-PostgreSQL-Database-Class/blob/master/USAGE.md
 */

require_once('../../../../conf/defines.php');
require_once('../../auth.php');
require_once('../../../../app/classes/PostgresDb.php');
require_once('../../../../app/classes/LogicalControls.php');

$opts = array(
    "db" => new \SeinopSys\PostgresDb(
        REPORTS_DATABASE_NAME,
        REPORTS_DATABASE_HOST,
        REPORTS_DATABASE_USER,
        REPORTS_DATABASE_PASSWORD
    ),
    "dir" => "/usr/share/TestContent/RubricatorTests/Cводный отчет/",
    "tableName" => "logical_controls",
    "filterWord" => "Портал",
    "code" => "WRF-0103",
    "entityType" => 0
);

try {
    $lc = new \ZabbixReports\LogicalControls($opts);
    $lc->update();
} catch (Exception $e) {
    header('Content-type: application/json');
    echo json_encode(array(
        "status"  => "error",
        "message" => $e->getMessage()
    ));
    exit();
}
