<?php

declare(strict_types=1);

use App\Models\{Category};

use Tischmann\Atlantis\{App, Date, DateTime, Locale, Time};


/**
 * Проверка на версию для слабовидящих
 *
 * @return boolean true - если версия для слабовидящих, false - если нет
 */
function is_visually_impaired()
{
    return cookies_get('vi') === 'true';
}

/**
 * Проверка на темный режим
 *
 * @return boolean true - если темный режим, false - если нет
 */
function is_dark_mode()
{
    return cookies_get('dark') === 'true';
}
/**
 * Возвращает версию ресурсов
 * 
 * @return string  - Версия ресурсов
 */
function get_resource_version(): string
{
    return App::getResourcesVersion();
}

/**
 * Получение значения куки
 *
 * @param string $name Имя куки
 * @return string|null Значение куки или null, если кука не существует
 */
function cookies_get(string $name): ?string
{
    $cookie = array_map('sanitize', $_COOKIE);

    return $cookie[$name] ?? null;
}

/**
 * Проверка наличия куки
 *
 * @param string $name Имя куки
 * @return boolean Если кука существует, то true, иначе false
 */
function cookies_has(string $name): bool
{
    return key_exists($name, $_COOKIE);
}

/**
 * Установка куки
 *
 * @param string $name Имя куки
 * @param string $value Значение куки
 * @param array $options Опции куки
 * @return bool true в случае успеха, иначе false
 */
function cookies_set(
    string $name,
    mixed $value,
    array $options = []
): bool {
    return setcookie(
        $name,
        strval($value),
        array_merge([
            'expires' => 0,
            'path' => strval(getenv('APP_COOKIE_PATH') ?: '/'),
            'secure' => boolval(getenv('APP_COOKIE_SECURE') ?: true),
            'httponly' => boolval(getenv('APP_COOKIE_HTTP_ONLY') ?: true),
            'samesite' => getenv('APP_COOKIE_SAMESITE') ?: 'Strict',
        ], $options)
    );
}

/**
 * Удаление куки
 *
 * @param string $name Имя куки
 * @param array $options Опции куки
 * @return bool true в случае успеха, иначе false
 */
function cookies_del(string $name, array $options = []): void
{
    cookies_set(
        $name,
        "",
        array_merge(
            [
                'path' => strval(getenv('APP_COOKIE_PATH') ?: '/'),
                'secure' => boolval(getenv('APP_COOKIE_SECURE') ?: true),
                'httponly' => boolval(getenv('APP_COOKIE_HTTP_ONLY') ?: true),
                'samesite' => getenv('APP_COOKIE_SAMESITE') ?: 'Strict',
            ],
            $options,
            [
                'expires' => time() - 3600
            ]
        )
    );
}

/**
 * Фильтрация данных переменной
 * 
 * @param string $value Переменная
 * @return mixed Отфильтрованная переменная
 */
function sanitize(mixed $value): mixed
{
    return match (gettype($value)) {
        'integer' => intval(filter_var($value, 519)),
        'double' => floatval(filter_var($value, 520)),
        'string' => strval(filter_var($value, 513)),
        default =>  $value,
    };
}

/**
 * Запуск сессии
 *
 * @return bool
 */
function session_init(
    string $name = 'PHPSESSID',
    ?string $id = null
): void {
    session_name($name);

    $id ??= session_id();

    session_id($id);

    session_start();
}

/**
 * Установка значения в сессию
 *
 * @param string $key Ключ
 * @param mixed $value Значение
 * @return void
 */
function session_set(string $key, mixed $value): void
{
    $_SESSION[$key] = $value;
}

/**
 * Проверка наличия значения в сессии
 *
 * @param string $key Ключ
 * @return bool true - если значение есть, false - если значения нет
 */
function session_has(string $key): bool
{
    return key_exists($key, $_SESSION ?? []);
}

/**
 * Проверка наличия значения в сессии
 *
 * @param string $key Ключ
 * 
 * @param callable $setter Функция, которая устанавливает значение в сессию
 * 
 * @return mixed Значение
 */
function session_find(string $key, callable $setter): mixed
{
    if (!session_has($key)) {
        $value = $setter();

        session_set($key, $value);

        return $value;
    }

    return session_get($key);
}

/**
 * Получение значения из сессии
 *
 * @param string $key Ключ
 * @return mixed Значение или null, если значения нет
 */
function session_get(string $key): mixed
{
    return $_SESSION[$key] ?? null;
}

/**
 * Удаление значения из сессии
 *
 * @param string $key Ключ
 * @return void
 */
function session_del(string $key): void
{
    if (session_has($key)) unset($_SESSION[$key]);
}

/**
 * Удаление всех значений из сессии
 *
 * @return void
 */
function session_kill(): void
{
    session_unset();
    session_regenerate_id();
}

function csrf_session_key(): string
{
    return 'ATLANTIS_CSRF_TOKENS_' . strval(getenv('APP_ID'));
}

/**
 * Удаляет все токены
 */
function csrf_flush(string $key = null)
{
    if ($key === null) {
        session_del(csrf_session_key());
    } else {
        $tokens = csrf_tokens();
        unset($tokens[$key]);
        session_set(csrf_session_key(), $tokens);
    }
}

/**
 * Возвращает токены
 * 
 * @return array Токены
 */
function csrf_tokens(): array
{
    return session_find(csrf_session_key(), function () {
        return [];
    });
}

/**
 * Устанавливает токен
 * 
 * @return object (object)[key => 'Key', token => 'Token']
 */
function csrf_set(): object
{
    $key = bin2hex(random_bytes(32));

    $token = bin2hex(random_bytes(32));

    $expires = time() + 3600;

    $tokens = csrf_tokens();

    $tokens[$key] = (object)['token' => $token, 'expires' => $expires];

    session_set(csrf_session_key(), $tokens);

    return (object)['key' => $key, 'token' => $token, 'expires' => $expires];
}

/**
 * Производит проверку токенов
 * 
 * @return bool Результат проверки
 */
function csrf_verify(): bool
{
    foreach (csrf_tokens() as $key => $token) {
        if ($token->expires < time()) csrf_flush($key);
    }

    $token = apache_request_headers()['X-Csrf-Token'] ?? null;

    if ($token !== null) {
        foreach (csrf_tokens() as $token_key => $token_value) {
            if ($token_value->token !== $token) continue;
            csrf_flush($token_key);
            return true;
        }
    }

    foreach (csrf_tokens() as $key => $token) {
        if (array_key_exists($key, $_REQUEST)) {
            if ($_REQUEST[$key] !== $token->token) continue;
            csrf_flush($key);
            return true;
        }
    }

    return false;
}

/**
 * Проверяет, что токен не прошел проверку
 * 
 * @return boolean
 */
function csrf_failed(): bool
{
    return !csrf_verify();
}

/**
 * Проверяет, что токен прошел проверку
 * 
 * @return boolean
 */
function csrf_passed(): bool
{
    return csrf_verify();
}

/**
 * Проверяет корректность строкового представления даты и времени
 * 
 * @param mixed $value Значение даты и времени в строковом представлении
 * 
 * @return bool true - корректно, false - некорректно
 */
function date_validate(mixed $value): bool
{
    if (!is_string($value)) return false;

    if (!$value) return false;

    try {
        $date = new DateTime($value);
    } catch (Exception $e) {
        return false;
    }

    $errors = $date::getLastErrors();

    return ($errors['warning_count'] ?? 0) + ($errors['error_count'] ?? 0) == 0;
}

/**
 * Возвращает типизированную переменную
 *
 * @param mixed $variable Переменная
 * @param string $type Тип переменной
 * @return mixed Типизированная переменная
 */
function typify(mixed $variable, string $type): mixed
{
    switch ($type) {
        case 'bool':
            return boolval($variable);
        case 'int':
            return intval($variable);
        case 'float':
            return floatval($variable);
        case 'array':
            if (is_array($variable)) return $variable;
            if (!is_string($variable)) return [];
            return json_decode($variable, true) ?? [];
        case 'object':
            if (is_object($variable)) return $variable;
            if (!is_string($variable)) return (object) [];
            return json_decode($variable) ?? (object) [];
        case 'Tischmann\Atlantis\DateTime':
            if (!date_validate($variable)) return new DateTime();
            return new DateTime($variable);
        case 'DateTime':
            if (!date_validate($variable)) return new \DateTime();
            return new \DateTime($variable);
        case 'Tischmann\Atlantis\Date':
            if (!date_validate($variable)) return new Date();
            return new Date($variable);
        case 'Tischmann\Atlantis\Time':
            if (!date_validate($variable)) return new Time();
            return new Time($variable);
        case 'string':
            return strval($variable);
        default:
            return $variable;
    }
}

/**
 * Возвращает тип данных свойства
 * 
 * @param object $object Объект или класс
 * @param string $property Имя свойства
 * @return string Тип данных
 */
function get_property_type(object $object, string $property): string
{
    if (!property_exists($object, $property)) return 'mixed';

    $reflectionProperty = new \ReflectionProperty($object, $property);

    $reflectionNamedType = $reflectionProperty->getType();

    assert($reflectionNamedType instanceof \ReflectionNamedType);

    return $reflectionNamedType?->getName() ?? 'mixed';
}

/**
 * Возвращает представление переменной для записи в БД
 *
 * @param object $object Объект или класс
 * @param mixed $variable Переменная
 * @return mixed Представление переменной или null
 */
function stringify_property(object $object, string $property): mixed
{
    $value = $object->{$property} ?? null;

    if ($value === null) return null;

    $type = get_property_type($object, $property);

    switch ($type) {
        case 'bool':
            return strval(intval($value));
        case 'int':
        case 'float':
            return strval($value);
        case 'array':
        case 'object':
            return json_encode($value, 32 | 256) ?: null;
        case 'Tischmann\Atlantis\DateTime':
        case 'DateTime':
            return strval($value->format('Y-m-d H:i:s'));
        case 'Tischmann\Atlantis\Date':
            return strval($value->format('Y-m-d'));
        case 'Tischmann\Atlantis\Time':
            return strval($value->format('H:i:s'));
        default:
            return strval($value);
    }
}

/**
 * Изменение размеров изображения
 * 
 * @param GdImage $image Изображение
 * @param int $dst_width Ширина на выходе
 * @param int $dst_height Высота на выходе
 */
function image_resize(
    GdImage $image,
    int $dst_width = 800,
    int $dst_height = 600
): GdImage|false {
    $image_width = imagesx($image);

    $image_height = imagesy($image);

    $source_width = $image_width;

    $source_height = $image_height;

    $source_ratio = $image_width / $image_height;

    $output_ratio = $dst_width / $dst_height;

    $src_x = 0;

    $src_y = 0;

    $dst_x = 0;

    $dst_y = 0;

    if ($source_ratio >= $output_ratio) {
        $source_width = intval($source_height * $output_ratio);
        $src_x = intval(($image_width - $source_width) / 2);
    } else {
        $source_height = intval($source_width / $output_ratio);
        $src_y = intval(($image_height - $source_height) / 2);
    }

    $output = imagecreatetruecolor($dst_width, $dst_height);

    if (!imagecopyresampled(
        $output,
        $image,
        $dst_x,
        $dst_y,
        $src_x,
        $src_y,
        $dst_width,
        $dst_height,
        $source_width,
        $source_height
    )) {
        return false;
    }

    return $output;
}

/**
 * Возвращает строку из файла локализации по ключу
 *
 * @param string $key Ключ
 * @param string $locale Локаль
 * @return string Строка из файла локализации
 */
function get_str(string $key, ?string $locale = null): string
{
    return Locale::get($key, $locale);
}

/**
 * Возвращает опции категорий для select
 *
 * @param Category $category Категория
 * @param int $selected Выбранная категория
 * @return array 
 */
function get_category_options(
    Category $category,
    int $selected = 0,
    ?int $exclude = null
): array {
    $options = [];

    if (
        ($exclude !== null && $category->id !== $exclude)
        || $exclude === null
    ) {
        $options[] = [
            'value' => $category->id,
            'text' => $category->title,
            'selected' => $category->id === $selected,
            'level' => $category->level
        ];
    }

    $category->children = $category->fetchChildren();

    foreach ($category->children as $child) {
        assert($child instanceof Category);

        if (
            ($exclude !== null && $child->id !== $exclude)
            || $exclude === null
        ) {
            $options[] = [
                'value' => $child->id,
                'text' => $child->title,
                'selected' => $child->id === $selected,
                'level' => $child->level
            ];
        }

        $child->children = $child->fetchChildren();

        if ($child->children) {
            $options = [
                ...$options,
                ...get_category_options($child, $selected)
            ];
        }
    }

    return $options;
}
