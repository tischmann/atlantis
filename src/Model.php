<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use Exception;

use Tischmann\Atlantis\{Table};

abstract class Model
{
    /**
     * Таблица модели
     *
     * @param Table
     */
    abstract public static function table(): Table;

    public function __construct(
        public int $id = 0,
        public ?DateTime $created_at = null,
        public ?DateTime $updated_at = null,
    ) {
        $this->__init();
    }

    /**
     * Запрос для модели
     *
     * @param Query
     */
    public static function query(): Query
    {
        return static::table()::query();
    }

    /**
     * Инициализация класса
     *
     * @return self
     */
    public function __init(): self
    {
        return $this;
    }

    public function __clone()
    {
        foreach ($this as $property => $value) {
            if (is_object($value)) {
                $reflectionClass = new \ReflectionClass($value);

                if ($reflectionClass->isCloneable()) {
                    $this->{$property} = clone $value;
                }
            }
        }
    }

    /**
     * Создаёт экземпляр класса
     *
     * @param array|object|null $fill Данные для заполнения свойств класса
     * @return self Экземпляр класса
     */
    public static function make(array|object|null $fill = null): static
    {
        $model = new static();

        if ($fill === null) return $model;

        return $model->__fill($fill);
    }

    /**
     * Заполняет свойства класса данными
     * Если свойство не существует или недоступно для записи, то оно игнорируется
     *
     * @param array|object|null $traversable Объект, который можно перебрать
     * 
     * @return self
     */
    public function __fill(array|object|null $traversable = null): self
    {
        if ($traversable === null) return $this->__init();

        if ($traversable instanceof Query) $traversable = $traversable->first();

        foreach ($traversable as $property => $value) {
            if (property_exists($this, $property)) {
                $value ??= null;

                $type = get_property_type($this, $property);

                $value = typify($value, $type);

                $this->{$property} = $value;
            }
        }

        return $this->__init();
    }

    /**
     * Проверяет, существует ли модель в базе данных
     *
     * @return boolean true если существует, false если нет
     */
    public function exists(): bool
    {
        return $this->id > 0;
    }

    /**
     * Сохраняет состояние модели в базе данных
     * 
     * если модель не определена, то добавляет ее, если определена, то обновляет
     *
     * @return boolean true если сохранена, false если нет
     */
    public function save(): bool
    {
        return $this->exists() ? $this->update() : $this->insert();
    }

    /**
     * Добавляет модель в базу данных
     *
     * @return boolean true если добавлена, false если нет
     */
    public function insert(): bool
    {
        if ($this->exists()) return $this->update();

        $values = [];

        $this->created_at = new DateTime();

        foreach ($this->table()->columnsNames() as $property) {
            if (property_exists($this, $property)) {
                $values[$property] = stringify_property($this, $property);
            }
        }

        $this->id = self::query()->insert($values);

        return $this->exists();
    }

    /**
     * Обновление модели в базе данных
     *
     * @return boolean true если обновлена, false если нет
     */
    public function update(): bool
    {
        $before = self::find($this->id);

        if (!$before->exists()) return false;

        $this->updated_at = new DateTime();

        $update = [];

        foreach ($this->table()->columnsNames() as $property) {
            if (!property_exists($this, $property)) continue;

            $old_value = stringify_property($before, $property);

            $new_value = stringify_property($this, $property);

            if ($old_value === $new_value) continue;

            $update[$property] = $new_value;
        }

        if (!$update) return true;

        $query = self::query()
            ->where('id', $this->id)
            ->limit(1);

        return $query->update($update);
    }

    /**
     * Удаление модели
     *
     * @param string $key Ключ
     * @return boolean true если удалена, false если нет
     */
    public function delete(string $key = 'id'): bool
    {
        return static::query()->where($key, $this->{$key})->limit(1)->delete();
    }

    /**
     * Поиск модели в базе данных по значению столбца(ов)
     *
     * @param mixed $value Значение столбца(ов)
     * @param string|array $column Столбец(цы)
     * @return self Модель
     */
    public static function find(mixed $value, string|array $column = 'id'): self
    {
        $query = self::query();

        $query->limit(1);

        $column = is_array($column) ? $column : [$column];

        foreach ($column as $col) $query->orWhere($col, $value);

        return self::make($query);
    }

    /**
     * Возвращает массив моделей из запроса
     *
     * @param Query $query Запрос
     * @param string $key Ключ
     * @return array Массив моделей
     */
    public static function fill(Query $query, string $key = 'id'): array
    {
        $array = [];

        foreach ($query->get() as $row) {
            $model = new static();

            $model->__fill($row);

            $array[$model->{$key}] = $model;
        }

        return $array;
    }
}
