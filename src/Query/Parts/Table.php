<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Parts;

use Falgun\Typo\Conditions\ConditionInterface;

final class Table implements TableLikeInterface
{

    private string $name;
    private string $alias;

    private function __construct(string $name, string $alias = '')
    {
        $this->name = $name;
        $this->alias = $alias;
    }

    public static function fromName(string $name): static
    {
        return new static($name);
    }

    public function as(string $alias): TableLikeInterface
    {
        $table = clone $this;

        $table->alias = $alias;

        return $table;
    }

    public function on(ConditionInterface $condition): JoinInterface
    {
        return Join::new($this)->on($condition);
    }

    public function using(ColumnLikeInterface $column): JoinInterface
    {
        return Join::new($this)->using($column);
    }

    public function getSQL(): string
    {
        return ($this->alias ? $this->name . ' as ' . $this->alias : $this->name);
    }

    public function getBindValues(): array
    {
        return [];
    }
}
