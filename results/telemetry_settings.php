<?php

// Detectar ambiente automaticamente
$isProduction = !in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1', '::1']) && 
                !strpos($_SERVER['HTTP_HOST'], '.local');

// Função para testar conexão MySQL
function testMySQLConnection($host, $user, $pass, $db, $port) {
    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5
        ]);
        $pdo = null; // Fechar conexão
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Configurar credenciais baseado no ambiente
if ($isProduction) {
    $mysql_config = [
        'username' => 'u533482233_speedtest',
        'password' => 'Speedtest2020',
        'hostname' => 'localhost',
        'database' => 'u533482233_speedtest',
        'port' => '3306'
    ];
} else {
    $mysql_config = [
        'username' => 'root',
        'password' => 'root',
        'hostname' => 'localhost',
        'database' => 'speedtest',
        'port' => '3306'
    ];
}

// Testar conexão MySQL e definir tipo de banco
if (testMySQLConnection($mysql_config['hostname'], $mysql_config['username'], 
                      $mysql_config['password'], $mysql_config['database'], 
                      $mysql_config['port'])) {
    // MySQL disponível
    $db_type = 'mysql';
    $MySql_username = $mysql_config['username'];
    $MySql_password = $mysql_config['password'];
    $MySql_hostname = $mysql_config['hostname'];
    $MySql_databasename = $mysql_config['database'];
    $MySql_port = $mysql_config['port'];
} else {
    // Fallback para SQLite
    $db_type = 'sqlite';
}
// Password to login to stats.php. Change this!!!
$stats_password = 'Tiago';
// If set to true, test IDs will be obfuscated to prevent users from guessing URLs of other tests
$enable_id_obfuscation = true;
// If set to true, IP addresses will be redacted from IP and ISP info fields, as well as the log
$redact_ip_addresses = false;

// Sqlite3 settings
$Sqlite_db_file = '../../speedtest_telemetry.sql';

// mssql settings
$MsSql_server = 'DB_HOSTNAME';
$MsSql_databasename = 'DB_NAME';
$MsSql_WindowsAuthentication = true;   //true or false
$MsSql_username = 'USERNAME';          //not used if MsSql_WindowsAuthentication is true
$MsSql_password = 'PASSWORD';          //not used if MsSql_WindowsAuthentication is true
$MsSql_TrustServerCertificate = true;  //true, false or comment out for driver default
//Download driver from https://docs.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server?view=sql-server-ver16

// Configurações padrão caso MySQL não esteja configurado acima
if (!isset($MySql_username)) {
    $MySql_username = 'root';
    $MySql_password = 'root';
    $MySql_hostname = 'localhost';
    $MySql_databasename = 'speedtest';
    $MySql_port = '3306';
}

// Postgresql settings
$PostgreSql_username = 'USERNAME';
$PostgreSql_password = 'PASSWORD';
$PostgreSql_hostname = 'DB_HOSTNAME';
$PostgreSql_databasename = 'DB_NAME';
