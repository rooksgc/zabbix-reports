<?php
define('APP_EXEC', 1);
require_once('conf/defines.php');
require_once('app/classes/Helper.php');
require_once('app/classes/Database.php');
require_once('app/classes/Authorization.php');

/**
 * Require template file
 */
require_once('app/views/portals.tpl');
