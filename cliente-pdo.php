<?php

class Connection
{
    public static function make($config)
    {
        try {
            $connection = new PDO("mysql:host=" . $config['host'] . ";port=" . $config['port'] . ";dbname=" . $config['database'],
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

$config = require("config.php");

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