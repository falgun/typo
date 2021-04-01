<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Update;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Parts\Table;
use Falgun\Typo\Query\Parts\Collection;
use Falgun\Typo\Interfaces\SQLableInterface;
use Falgun\Typo\Interfaces\ConditionInterface;
use Falgun\Typo\Query\Parts\Condition\ConditionGroup;

final class UpdateQueryStep2 implements SQLableInterface
{

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Kuery $kuery;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Table $table;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private array $joins;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private array $updatableColumns;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private array $updatableValues;
    private ConditionGroup $conditionGroup;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private function __construct()
    {
        $this->conditionGroup = ConditionGroup::fromBlank();
    }

    public static function fromCondition(
        Kuery $kuery,
        Table $table,
        array $joins,
        array $columns,
        array $values,
        ConditionInterface $condition
    ): UpdateQueryStep2
    {
        $object = new static;
        $object->kuery = $kuery;
        $object->table = $table;
        $object->joins = $joins;
        $object->updatableColumns = $columns;
        $object->updatableValues = $values;
        $object->conditionGroup = ConditionGroup::fromFirstCondition($condition);

        return $object;
    }

    public function andWhere(ConditionInterface $condition): UpdateQueryStep2
    {
        $this->conditionGroup->and($condition);

        return $this;
    }

    public function orWhere(ConditionInterface $condition): UpdateQueryStep2
    {
        $this->conditionGroup->or($condition);

        return $this;
    }

    public function execute(): int
    {
        $stmt = $this->kuery->run($this->getSQL(), $this->getBindValues());

        return $stmt->affected_rows;
    }

    public function getSQL(): string
    {
        $sql = 'UPDATE ' . $this->table->getSQL() . PHP_EOL;

        foreach ($this->joins as $join) {
            $sql .= $join->getSQL() . PHP_EOL;
        }

        $parts = [];
        foreach ($this->updatableColumns as $i => $column) {
            if ($this->updatableValues[$i] instanceof SQLableInterface) {
                $parts[] = $column->getSQL() . ' = ' . $this->updatableValues[$i]->getSQL();
            } else {
                $parts[] = $column->getSQL() . ' = ?';
            }
        }

        $sql .= 'SET ' . implode(', ', $parts);

        $sql .= $this->conditionGroup->getSQL();

        return $sql;
    }

    public function getBindValues(): array
    {
        $binds = [];

        foreach ($this->joins as $join) {
            $binds = [...$binds, ...$join->getBindValues()];
        }

        $binds = [...$binds, ...array_filter(
                $this->updatableValues,
                fn($value) => !($value instanceof SQLableInterface))
        ];

        $binds = [...$binds, ...$this->conditionGroup->getBindValues()];

        return $binds;
    }
}
