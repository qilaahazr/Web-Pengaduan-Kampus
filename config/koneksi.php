<?php

// Load environment variables from .env file if exists
$env_file = dirname(__DIR__) . '/.env';
if(file_exists($env_file)){
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($lines as $line){
        if(strpos($line, '#') === 0) continue;
        if(strpos($line, '=') !== false){
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

$host = $_ENV['DB_HOST'] ?? 'localhost';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';
$db   = $_ENV['DB_NAME'] ?? 'db_pengaduan';

$conn = mysqli_connect($host, $user, $pass, $db);

if(!$conn){
    die(mysqli_connect_error());
}

// Set secure MySQL options
mysqli_set_charset($conn, 'utf8mb4');

?>