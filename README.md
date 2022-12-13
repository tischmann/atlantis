# Atlantis

Atlantis — это простой и легкий MVC PHP-фреймворк.

Он разработан, чтобы быть простым в использовании и легким в освоении.

Он также спроектирован таким образом, чтобы его можно было легко расширять и настраивать.

Это отличный фреймворк как для новичков, так и для экспертов.

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

/bootstap/directives.php — файл директив шаблонизатора

/src — директория ядра

/lang — директория языков

/public — директория публичных файлов

/public/index.php — файл точки входа

/public/app.js — главный файл JavaScript

/public/pwa.js — файл Progressive Web App

/public/app.css — главный файл CSS

/routes — директория маршрутов

/private.pem - приватный ключ для JWT

/public.pem - публичный ключ для JWT

## Настройка

### Настройка окружения

APP_DEBUG — режим отладки (по умолчанию 0)

APP_ID — идентификатор приложения (уникальные для каждого приложения)

APP_TIMEZONE — часовой пояс приложения (по умолчанию UTC)

APP_COOKIE_LIFETIME — время жизни куки (по умолчанию 0)

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

Для всех форм автоматически генерируется токен и добавляется в скрытое поле.

Для проверки токенов используется метод:

```php
CSRF::verify(Request $request)
```

В качестве параметра передается объект запроса.

Метод не возвращает никаких значений, но при несовпадении токенов выбрасывает исключение.

После верификации токенов, они удаляются из сессии.

## Шаблонизатор представлений

Шаблоны представлений хранятся в директории /app/Views и имеют расширение .tpl.

### Структура шаблонов

#### Лэйауты

Лэйауты хранятся в директории /app/Views/layouts и имеют расширение .tpl.

Для подключения секций в лэйауте используется метод: {{yield=section_name}}

Пример:

```html
<!DOCTYPE html>
<html lang="ru">
    <head></head>
    <body>
        {{yield=body}}
    </body>
</html>
```

где {{yield=body}} - тело страницы, которое будет подключено в шаблоне из секции {{section=body}}

В файлах шаблонов для подключения лэйаута используется метод: {{layout=default}}

Пример:

```html
{{layout=default}} {{section=body}}
<h1>Привет, мир!</h1>
{{/section}}
```

#### Секции

Для определения секций используется метод: {{section=section_name}} c закрывающим тегом {{/section}}.

Пример:

```html
{{section=body}}
<h1>Привет, мир!</h1>
{{/section}}
```

Секции выводятся в лэйауте с помощью метода: {{yield=section_name}}

#### Переменные окружения

Для передачи переменных в шаблон используется метод: {{env=variable_name}}

Пример:

```html
<title>{{env=app_title}}</title>
```

#### Локализация

Для локализации используется метод: {{lang=key_name}}

Пример:

```html
<h1>{{lang=hello_world}}</h1>
```

Локализации хранятся в директории /lang.

Каждая локализация хранится в отдельном файле с названием локали и расширением .php.

Наприер: /lang/ru.php - русская локализация.

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

Для вывода простых переменных используется метод: {{$var_name}}.

Для вывода свойства объекта: {{$object->property}}.

Для вывода значения метода объекта: {{$object->method(arg1,arg2,arg3)}}.
Аргументы метода должны представлять собой простые строковые значения.

Все переменные должны быть переданы в шаблон при объявлении представления.

Пример:

```php
new View('view_name', [
    'var_name' => 'Привет, мир!',
    'object' => class {
        public $property = 'Привет, мир!';
        public function method($arg1, $arg2, $arg3) {
            return 'Some text';
        }
    }
    ]);
```

Если переменная не передана в шаблон, то отобразится строка шаблона.

#### Подгрузка шаблонов

Для подгрузки шаблонов используется метод: {{include=template/path}}

Пример:

```html
{{include=header}}
```

#### Загрузка содержимого файла

Для загрузки содержимого файла используется метод: {{load=file/path.ext}}

Пример:

```html
{{load=content.txt}}
```

#### Директивы

##### Условия

Для вывода условного блока используется метод: {{if condition}} c закрывающим тегом {{/if}}.

где condition - условие, которое должно вернуть true или false.

Пример:

```html
{{if $number == 1}}
<h1>Привет, мир!</h1>
{{/if}}
```

Возможные примеры условий:

== - равно

!= - не равно

'>' - больше

'<' - меньше

'>=' - больше или равно

'<=' - меньше или равно

'in' - входит в массив (значения через запятую)

'!in' - не входит в массив (значения через запятую)

Установка условий не обязательно, можно использовать только теги {{if condition}} и {{/if}}.

Также возможно использование логического оператора отрицания: !.

Возможно использование нескольких условий в одном блоке с объединением их с помощью логического оператора ||.

Пример:

```html
{{if $number == 1||2||3}}
<h1>Привет, мир!</h1>
{{/if}}
```

Одновременно можно использовать только один логический оператор.

Пример:

```html
{{if !$number}}
<h1>Привет, мир!</h1>
{{/if}}
```

##### Циклы

Для вывода цикла используется метод: {{each $var as $key => $value}} c закрывающим тегом {{/each}}.

где $var - переменная, которая должна быть массивом, $key - ключ массива, $value - значение массива.

Также можно использовать метод без ключа: {{each $var as $value}} c закрывающим тегом {{/each}}.

Вывод итерируемых переменных осуществляется с помощью метода: {{$value}} для значения и {{$key}} для ключа соответственно.

Название переменных $key и $value может быть любым, в соответствии с правилами именования переменных PHP.

Пример:

```html
{{each $users as $user}}
<h1>Привет, {{$user}}!</h1>
{{/each}}
```

##### Доступные директивы

Для вывода переменных используется метод: {{$var}}.

Для вывода реферера используется метод: {{referrer}}.

Для вывода токена nonce используется метод: {{nonce}}.

Для вывода заголовка страницы используется метод: {{title}}.

Для вывода даты используется метод: {{date(format)}}.

Для вывода уникального идентификатора используется метод: {{uniqid}}.

Для проверки авторизации используется метод: {{if auth}} {{/if}}.

Для проверки роли администратора используется метод: {{if admin}} {{/if}}.

##### Пользовательские директивы условий

Для создания пользовательских директив условий используется метод: {{if directive}} c закрывающим тегом {{/if}}.

Пользовательские директивы условий проверяю только на true.

Можно использовать логическое отрицание: !, например: {{if !directive}}{{/if}}.

где directive - название директивы.

Пример:

Объявление директивы:

```php
View::directive('is_admin', function() {
    return Auth::user()->isAdmin();
});
```

Использование директивы:

```html
{{if is_admin}}
<h1>Привет, админ!</h1>
{{/if}}
```

#### Пользовательские директивы

Для создания пользовательских директив используется метод: {{directive(arg1,arg2,arg3)}}.

где directive - название директивы, arg1, arg2, arg3 - аргументы.

Аргументы должны быть простыми строками.

Аргументы можно опустить, например: {{directive}}.

Пример:

Объявление директивы:

```php
View::directive('date', function($format) {
    return date($format);
});
```

Использование директивы:

```html
{{date('d.m.Y')}}
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

## Статьи

### Структура

Статьи хранятся в таблице: articles.

Изображения для статей хранятся в папке: /public/images/articles/id, где id - id статьи.

Изображения краткой и полной версии статьи хранятся в подпапке images - /public/images/articles/id/images, где id - id статьи.

Для сохранения изображений необходимо предоставить права на запись в папку: /public/images/articles.

необходимо предоставить права на запись в папку: /public.

```bash
sudo chown -R www-data:www-data /path/to/public
sudo chmod -R g+rw /path/to/public
```
