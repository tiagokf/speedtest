<?php

// Type of db: "mssql", "mysql", "sqlite" or "postgresql"
$db_type = 'mysql';
// Password to login to stats.php. Change this!!!
$stats_password = 'Tiago';
// If set to true, test IDs will be obfuscated to prevent users from guessing URLs of other tests
$enable_id_obfuscation = true;
// If set to true, IP addresses will be redacted from IP and ISP info fields, as well as the log
$redact_ip_addresses = false;

// Sqlite3 settings
$Sqlite_db_file = '../../speedtest_telemetry.sql';

// mssql settings
$MsSql_server = 'localhost';
$MsSql_databasename = 'u533482233_speedtest';
$MsSql_WindowsAuthentication = true;   //true or false
$MsSql_username = 'u533482233_speedtest'; //not used if MsSql_WindowsAuthentication is true
$MsSql_password = 'Speedtest2020'; //not used if MsSql_WindowsAuthentication is true
$MsSql_TrustServerCertificate = true;  //true, false or comment out for driver default
//Download driver from https://docs.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server?view=sql-server-ver16

// Mysql settings - CONFIGURE PARA PRODUÇÃO
$MySql_username = 'u533482233_speedtest';
$MySql_password = 'Speedtest2020';
$MySql_hostname = 'localhost'; // ou IP do servidor MySQL
$MySql_databasename = 'u533482233_speedtest';
$MySql_port = '3306';

// Postgresql settings
$PostgreSql_username = 'USERNAME';
$PostgreSql_password = 'PASSWORD';
$PostgreSql_hostname = 'DB_HOSTNAME';
$PostgreSql_databasename = 'DB_NAME';