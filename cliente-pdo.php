<?php

class Connection
{
    public static function make($config)
    {
        try {
            $connection = new PDO(
                "mysql:host=" . $config['host'] . ";port=" . $config['port'] . ";dbname=" . $config['database'],
                $config['user'],
                $config['password'],
                $config['options']
            );
        } catch (PDOException $PDOException) {
            die($PDOException->getMessage());
        }
        return $connection;
    }
}


// Carga las variables desde .env si existe (solo en desarrollo)
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        [$name, $value] = explode('=', $line, 2);
        putenv("$name=$value");
    }
}

// Obtén las variables (ya sea del .env en local o de Render en producción)
$db_host = getenv("DB_HOST");
$db_port = getenv("DB_PORT");
$db_name = getenv("DB_NAME");
$db_user_escritor = getenv("DB_USER_ESCRITOR");
$db_pass_escritor = getenv("DB_PASS_ESCRITOR");
$db_user_consultor = getenv("DB_USER_CONSULTOR");
$db_pass_consultor = getenv("DB_PASS_CONSULTOR");



$config= [

    'aiven' => [
        'database' => $db_name,
        'user' => $db_user_escritor,
        'password' => $db_pass_escritor,
        'host' => $db_host,
        'port' => $db_port,
        'options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => true
        ]

    ],

    'aiven-consultor' => [
        'database' => $db_name,
        'user' => $db_user_consultor,
        'password' => $db_pass_consultor,
        'host' => $db_host,
        'port' => $db_port,
        'options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => true


        ]

    ]


];



$connection_sql = Connection::make($config['aiven']);
$connection_sql_consultor = Connection::make($config['aiven-consultor']);

/***Función que ejecuta una consulta sql***/
function consulta_pdo_sql($consulta, $array_valores = null)
{
    global $connection_sql_consultor;
    $pdoStatement = $connection_sql_consultor->prepare($consulta);
    $pdoStatement->execute($array_valores);
    return $pdoStatement->fetchAll();
}

/***Función que ejecuta un comando sql***/
function comando_pdo_sql($comando, $array_valores = null)
{
    global $connection_sql;
    $pdoStatement = $connection_sql->prepare($comando);
    $pdoStatement->execute($array_valores);
}
