<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

/**
 * Маршрут
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
final class Route
{
    public array $uri = []; // URI маршрута

    public const REGEX_ARGS = "/^\{(\?)?(\w+)\}$/"; // Регулярное выражение для аргументов
    /**
     * @param Controller $controller Контроллер маршрута
     * @param string $action Метод контроллера
     * @param string $method Метод запроса
     * @param string $path Путь
     * @param string $accept Тип данных (html, json, text)
     * @param string $type Тип данных (html, json, text, form)
     * @param string $title Заголовок страницы 
     * @param array $args Аргументы запроса
     */
    public function __construct(
        public ?Controller $controller = new Controller(),
        public string $action = 'index',
        public string $method = 'ANY',
        public string $path = '',
        public string $accept = 'any',
        public string $type = 'any',
        public string $title = '',
        public array $args = [],
    ) {
        $this->method = strtoupper($this->method);
        $this->accept = strtolower($this->accept);
        $this->type = strtolower($this->type);
        $this->uri = explode('/', $this->path);
    }

    /**
     * Валидация маршрута
     *
     * @param array $uri URI запроса
     * @return bool true - маршрут валиден, false - маршрут не валиден
     */
    public function validate(array $uri): bool
    {
        if (!$uri) return true;

        if (count($uri) !== count($this->uri)) return false;

        $args = [];

        foreach ($this->uri as $index => $chunk) {
            if (!preg_match(self::REGEX_ARGS, $chunk, $matches)) continue;

            $value = $uri[$index];

            $optional = $matches[1] === '?';

            $key = $matches[2];

            if (empty($value) && !$optional) continue;

            $this->uri[$index] = $args[$key] = $value;
        }

        if (array_diff($uri, $this->uri)) return false;

        $this->args = array_merge($this->args, $args);

        return true;
    }

    /**
     * Выполнение маршрута
     *
     * @return void
     */
    public function resolve(Request $request): void
    {
        foreach ($this->args as $key => $value) {
            $request->route($key, $value);
        }

        if ($this->title) putenv("APP_TITLE={$this->title}");

        $this->controller->{$this->action}($request);
    }
}