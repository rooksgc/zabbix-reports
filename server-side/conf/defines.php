<?php
# Paths
define('APP_PATH', dirname(__DIR__ . '../'));

# Connection to `zabbix` database (postgres)
define('ZABBIX_DATABASE_HOST', 'localhost');
define('ZABBIX_DATABASE_PORT', '5432');
define('ZABBIX_DATABASE_NAME', '');
define('ZABBIX_DATABASE_USER', '');
define('ZABBIX_DATABASE_PASSWORD', '');

# Connection to `reports` database (postgres)
define('REPORTS_DATABASE_HOST', 'localhost');
define('REPORTS_DATABASE_PORT', '5432');
define('REPORTS_DATABASE_NAME', '');
define('REPORTS_DATABASE_USER', '');
define('REPORTS_DATABASE_PASSWORD', '');

# Analyser
define('ANALYZER_WARNING_INTERVAL', '3900');

# API
define('API_TOKEN', '');