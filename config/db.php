<?php
require_once __DIR__ . '/config.php';

class Database {
  private static $instance = null;
  public static function getConnection(): PDO {
    if (self::$instance === null) {
      $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
      $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      ];
      self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    return self::$instance;
  }
}
