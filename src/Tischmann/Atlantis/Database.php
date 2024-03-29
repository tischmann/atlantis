<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use Exception;

use PDO;

use PDOException;

/**
 * Класс для работы с базой данных
 */
final class Database extends PDO
{
    /**
     * Конструктор класса
     * 
     * @param string $dsn Строка подключения к базе данных
     * @param string $username Имя пользователя базы данных
     * @param string $password Пароль пользователя базы данных
     * @param array $options Массив опций подключения к базе данных
     * 
     * @throws Exception
     */
    public function __construct(
        string $dsn = "mysql:host=localhost;dbname=;charset=utf8;port=3306",
        ?string $username = null,
        ?string $password = null,
        ?array $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => true,
            PDO::ATTR_PERSISTENT => true,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false
        ]
    ) {
        try {
            parent::__construct($dsn, $username, $password, $options);
        } catch (PDOException $exception) {
            throw new Exception($exception->getMessage(), 500);
        }
    }

    public static function connect(
        ?string $type = null,
        ?string $host = null,
        ?string $port = null,
        ?string $name = null,
        ?string $charset = null,
        ?string $username = null,
        ?string $password = null
    ): self {
        $type ??= strval(getenv('DB_TYPE'));

        $host ??= strval(getenv('DB_HOST'));

        $port ??= strval(getenv('DB_PORT') ?? 3306);

        $name ??= strval(getenv('DB_NAME'));

        $charset ??= strval(getenv('DB_CHARSET') ?? 'utf8');

        $username ??= strval(getenv('DB_USERNAME'));

        $password ??= strval(getenv('DB_PASSWORD'));

        return new self(
            dsn: "{$type}:host={$host};dbname={$name};charset={$charset};port={$port}",
            username: strval(getenv('DB_USERNAME')),
            password: strval(getenv('DB_PASSWORD')),
        );
    }

    /**
     * Выполнение запроса к базе данных
     * 
     * @param string $statement Текст запроса
     * @param array $values Массив значений для подстановки в запрос 
     * @return bool true в случае успеха, иначе false
     * @throws Exception
     */
    public function execute(string $statement, array $values = []): bool
    {
        try {
            return $this->prepare($statement)->execute($values);
        } catch (PDOException $exception) {
            throw new Exception($exception->getMessage(), 500);
        }
    }

    /**
     * Выполнение запроса на выборку значений из базы данных
     * 
     * @param string $statement Текст запроса
     * @param array $values Массив значений для подстановки в запрос
     * @return array Массив объектов данных
     * @throws Exception
     */
    public function fetchAll(string $statement, array $values = []): array
    {
        try {
            $pdoStatement = $this->prepare($statement);
            $pdoStatement->execute($values);
            return $pdoStatement->fetchAll();
        } catch (PDOException $exception) {
            throw new Exception($exception->getMessage(), 500);
        }
    }

    /**
     * Выполнение запроса на выборку значения одного столбца из базы данных
     * 
     * @param string $statement Текст запроса
     * @param array $values Массив значений для подстановки в запрос
     * @return mixed Значение столбца 
     * @throws Exception
     */
    public function fetchColumn(
        string $statement,
        array $values = [],
        int $column = 0
    ): mixed {
        try {
            $pdoStatement = $this->prepare($statement);
            $pdoStatement->execute($values);
            return $pdoStatement->fetchColumn($column);
        } catch (PDOException $exception) {
            throw new Exception($exception->getMessage(), 500);
        }
    }
}
