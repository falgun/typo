<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Parts;

use Falgun\Typo\Conditions\Equal;
use Falgun\Typo\Interfaces\ColumnLikeInterface;

final class Column implements ColumnLikeInterface
{

    private string $name;
    private string $alias;
    private string $type;
    private bool $nullable;
    private string $key;
    private string $default;
    private string $extra;

    private function __construct(
        string $name,
        string $alias,
        string $type,
        bool $nullable,
        string $key,
        string $default,
        string $extra
    )
    {
        $this->name = $name;
        $this->alias = $alias;
        $this->type = $type;
        $this->nullable = $nullable;
        $this->key = $key;
        $this->default = $default;
        $this->extra = $extra;
    }

    public static function fromSchema(
        string $name,
        string $type = 'varchar',
        bool $nullable = false,
        string $key = '',
        string $default = '',
        string $extra = ''
    ): static
    {
        return new static($name, $name, $type, $nullable, $key, $default, $extra);
    }

    public function as(string $alias): ColumnLikeInterface
    {
        $this->alias = $alias;

        return $this;
    }

    public function getSQL(): string
    {
        if ($this->name === $this->alias) {
            return $this->name;
        }

        return $this->name . ' as ' . $this->alias;
    }

    public function getBindValues(): array
    {
        return [];
    }

    /**
     *
     * @param mixed $value
     *
     * @return Equal
     */
    public function eq($value): Equal
    {
        return Equal::fromSides($this, $value);
    }
}
