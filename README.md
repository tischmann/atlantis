# Atlantis

Atlantis — это простой и легкий MVC PHP-фреймворк.

Он разработан, чтобы быть простым в использовании и легким в освоении.

Он также спроектирован таким образом, чтобы его можно было легко расширять и настраивать.

Это отличный фреймворк как для новичков, так и для экспертов.

# Системные требования

OS: Linux

PHP: 8.2+ (FPM)

MySQL: 8.0+ (Рекомендуется)

Nginx: 1.18.0+

## Структура файловой системы

/.env — файл окружения

/.gitignore — файл исключений Git

/README.md — файл описания

/app — директория приложения

/app/Database — директория базы данных

/app/Controllers — директория контроллеров

/app/Models — директория моделей

/app/Views — директория представлений

/bootstrap — директория загрузки

/bootstrap/app.php — файл загрузки приложения

/bootstrap/config.php — файл конфигурации приложения

/bootstrap/require.php — файл зависимостей приложения

/bootstap/routes.php — файл маршрутизации приложения

/src — директория ядра

/lang — директория языков

/public — директория публичных файлов

/public/index.php — файл точки входа

/public/app.js — главный файл JavaScript

/public/pwa.js — файл Progressive Web App

/public/app.css — главный файл CSS

/routes — директория маршрутов

/private.pem - приватный ключ

/public.pem - публичный ключ

## Настройка

### Настройка окружения

APP_ID — идентификатор приложения (уникальные для каждого приложения)

APP_TIMEZONE — часовой пояс приложения (по умолчанию UTC)

APP_COOKIE_PATH — путь куки (по умолчанию /)

APP_COOKIE_HTTP_ONLY — куки доступны только через HTTP (по умолчанию 1)

APP_USE_ONLY_COOKIES — использовать только куки (по умолчанию 1)

APP_COOKIE_SECURE — использовать защищенные куки (по умолчанию 1)

APP_USE_STRICT_MODE — использовать строгий режим (по умолчанию 1)

APP_COOKIE_SAMESITE — использовать строгий режим куки (по умолчанию Strict)

APP_USE_TRANS_SID — использовать SID в куках (по умолчанию 0)

APP_CACHE_LIMITER — лимитер кэша (по умолчанию nocache)

APP_SID_LENGTH — длина SID (по умолчанию 128)

APP_HASH_FUNCTION — функция хеширования (по умолчанию sha256)

APP_LOCALE — локаль приложения (ru, en, ...)

APP_TITLE — заголовок приложения

APP_DESCR — описание приложения

DB_TYPE — тип базы данны

DB_HOST — хост базы данных

DB_PORT — порт базы данных

DB_NAME — имя базы

DB_CHARSET — кодировка базы данных

DB_USERNAME — пользователь базы данных

DB_PASSWORD — пароль базы данных

MEMCACHED_HOST — хост Memcached

MEMCACHED_PORT — порт Memcached

## CSRF защита

Для защиты от CSRF атак используется специальный токен.

Токен генерируется при каждом запросе и хранится в сессии.

Для генерации токена в форме используется шаблон {{csrf}}.

Для вывода значения токена CSRF используется метод: {{csrf-token}}.

Для проверки токенов используется метод:

```php
csrf_verify();
```

В качестве параметра передается объект запроса.

Метод не возвращает никаких значений, но при несовпадении токенов выбрасывает исключение.

После верификации токенов, они удаляются из сессии.

## Роутинг

Роутинг осуществляется в файле /bootstrap/routes.php.

Маршруты задаются в директории /routes в виде файлов с расширением .php.

### Пример маршрута

```php
Router::add(
    new Route(
        controller: new Controller(),
        path: 'path',
        action: 'action',
        method: 'GET'
    )
);
```

### Параметры маршрута

Переменные, переданные в маршруте, доступны в контроллере через свойство route, являющеся экземпляром класса Route.

Пример:

```php
class SomeController extends Controller
{
    public function someAction()
    {
        $args = $this->route->args();
    }
}
```

### Параметры запроса

Параметры запроса доступны в контроллере экземпляр класса Request.

Пример:

```php
class SomeController extends Controller
{
    public function someAction()
    {
        $request = Request::instance();

        $post = $request->post();

        $get = $request->get();

        $input = $request->input();

        $headers = $request->headers();

        $request = $request->request();
    }
}
```

## Шаблонизатор представлений

Шаблоны представлений хранятся в директории /app/Views и имеют расширение .php.

### Структура шаблонов

#### Переменные окружения

Для передачи переменных в шаблон используется метод: {{env=VARIABLE_NAME}}.

Регистр переменных имеет значение.

Пример:

```html
<title>{{env=APP_TITLE}}</title>
```

#### Локализация

Для локализации используется метод: {{lang=key_name}}

Пример:

```html
<h1>{{lang=hello_world}}</h1>
```

Локализации хранятся в директории /locales.

Каждая локализация хранится в отдельном файле с названием локали и расширением .php.

Наприер: /locales/ru.php - русская локализация.

Файлы локализации возвращают массив с ключами и значениями.

Ключи приводятся к нижнему регистру и могут содержать символы: a-z, 0-9, \_.

Пример:

```php
<?php
return [
    'hello_world' => 'Привет, мир!'
];
```

Файлов локализации может быть несколько, они будут объединены в один массив.

#### Переменные

Для вывода строковых переменных используется метод: {{var_name}}.

Все переменные должны быть переданы в шаблон при объявлении представления.

Пример:

```php
new Template(
    'template_name',
    [
        'var_name' => 'Привет, мир!',
    ]
);
```

## База данных

### Структура таблиц

Структура таблиц хранится в папке: /app/Database.

В папке находятся файлы с именами таблиц и расширением .php.

### Создание

Для создания таблиц используется команда:

```bash
./migrate create
```

### Удаление

Для удаления всех таблиц используется команда:

```bash
./migrate remove
```

### Заполнение

Для заполнения таблиц данными используется команда:

```bash
./migrate seed
```
