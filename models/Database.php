<?php
class Database {
    private static $pdo;

    public static function connect() {
        if (!isset(self::$pdo)) {
            $host = getenv('DB_HOST');
            $db = getenv('DB_NAME');
            $user = getenv('DB_USER');
            $pass = getenv('DB_PASS');
            $charset = 'utf8mb4';

			// Data Source Name
            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

			// 에러 처리, 쿼리 결과 연관 배열로 반환, Prepared Staments 사용
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$pdo = new PDO($dsn, $user, $pass, $options);
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int)$e->getCode());
            }
        }
        return self::$pdo;
    }
}
