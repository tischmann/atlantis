<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use Memcache;

class Cache
{
    private static Memcache $memcache;

    private function __construct()
    {
    }

    /**
     * Устанавливает значение в кэш
     *
     * @param string $key Ключ
     * @param mixed $value Значение
     * @param boolean $copmress Сжимать ли значение
     * @param integer $expire Время жизни кэша в секундах
     * @return boolean
     */
    public static function set(
        string $key,
        mixed $value,
        bool $copmress = true,
        int $expire = 0,
    ): bool {
        return static::connect()->set(
            static::prefix() . $key,
            $value,
            $copmress ? MEMCACHE_COMPRESSED : 0,
            $expire
        );
    }

    /**
     * Получает значение из кэша по ключу, а в случае отстутствия значения устанавливает его
     *
     * @param string $key Ключ
     * @param callable $setter Функция, которая возвращает значение для установки в кэш
     * @param boolean $compress Сжимать ли значение
     * @param integer $expire Время жизни кэша в секундах
     * @return mixed
     */
    public static function find(
        string $key,
        callable $setter = null,
        bool $copmress = true,
        int $expire = 0
    ): mixed {
        if (!static::has($key) && is_callable($setter)) {
            static::set($key, $setter(), $copmress, $expire);
        }

        return static::get($key);
    }

    /**
     * Проверяет наличие значения в кэше
     *
     * @param string $key Ключ
     * @return bool true, если значение есть в кэше, иначе false
     */
    public static function has(string $key): bool
    {
        return static::get($key) !== false;
    }

    /**
     * Получает значение из кэша по ключу
     *
     * @param string $key Ключ
     * @return string|false Возвращает значение из кэша или false, если значение не найдено
     */
    public static function get(string $key): string|false
    {
        return static::connect()->get(static::prefix() . $key);
    }

    /**
     * Удаляет все значения из кэша
     *
     * @return boolean true, если кэш очищен, иначе false
     */
    public static function flush(): bool
    {
        return static::connect()->flush();
    }

    /**
     * Удаляет значение из кэша по ключу
     *
     * @param string $key Ключ
     * @return boolean true, если значение удалено, иначе false
     */
    public static function delete(string $key): bool
    {
        return static::connect()->delete(static::prefix() . $key);
    }

    /**
     * Устанавливает подключение к серверу кэша
     * 
     * @return Memcache Объект подключения к серверу кэша
     */
    private static function connect(): Memcache
    {
        if (!isset(static::$memcache)) {
            static::$memcache = new Memcache();
            static::$memcache->connect(
                strval(getenv('MEMCACHED_HOST')),
                intval(getenv('MEMCACHED_PORT'))
            );
        }

        return static::$memcache;
    }

    /**
     * Возвращает префикс ключа кэша
     *
     * @return string
     */
    private static function prefix(): string
    {
        return "atlantis_" . getenv('APP_ID') . "_";
    }
}