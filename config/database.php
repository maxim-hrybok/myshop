<?php

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__. '/..');
$dotenv->load();
//in .env file we have DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_CHARSET

$host = $_ENV['DB_HOST'];
$db   = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];
$charset = $_ENV['DB_CHARSET'];

// 1. timezone in php for such functions as time() or date() now they will WORK in utc)
date_default_timezone_set('UTC');


try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Set timezone for SQL for functions like NOW() to match with PHP functions . // brute force protection relies on this consistency.
    $pdo->exec("SET time_zone = '+00:00'");
} catch (PDOException $e) {
    // On a live server, log the actual error to a file and show a friendly message.
    // For now, a generic message is much safer than showing the real error.
    // error_log('Database Connection Error: ' . $e->getMessage()); // (Advanced)


    //die("A website error has occurred. Please try again later.");

    die("DB Connection Failed: " . $e->getMessage());
}

//} catch (PDOException $e) {
//    // IMPORTANT: Only for development. Never show this on a live server.
//    throw new \PDOException($e->getMessage(), (int)$e->getCode());
//}
?>