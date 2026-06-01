<?php
// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/php/error.log');

// Función para obtener variables de entorno de manera segura
function getEnvVar($name, $default = null) {
    return getenv($name) ?: $default;
}

// Configuración de la base de datos
$dbConfig = [
    'host'     => getEnvVar('MSSQL_HOST', 'db'),
    'database' => getEnvVar('MSSQL_DATABASE'),
    'username' => getEnvVar('MSSQL_USER'),
    'password' => getEnvVar('MSSQL_PASSWORD')
];

// Funciones de base de datos
function connectDB($config) {
    try {
        $dsn = "sqlserv:Server={$config['host']};Database={$config['database']}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        return new PDO($dsn, $config['username'], $config['password'], $options);
    } catch (PDOException $e) {
        print_r($e);
        error_log("Error de conexión: " . $e->getMessage());
        return null;
    }
}

function getPersons($db) {
    try {
        $stmt = $db->query("SELECT id, name FROM Person ORDER BY id");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error al obtener personas: " . $e->getMessage());
        return [];
    }
}

// Inicializar conexión
$db = connectDB($dbConfig);
$dbStatus = $db ? 'Conectado' : 'Error de conexión';

// Información del sistema
$phpVersion = phpversion();
$serverInfo = $_SERVER['SERVER_SOFTWARE'];
