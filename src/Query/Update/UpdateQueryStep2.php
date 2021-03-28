<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Update;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Parts\Table;
use Falgun\Typo\Query\Parts\Column;
use Falgun\Typo\Interfaces\SQLableInterface;
use Falgun\Typo\Interfaces\ConditionInterface;

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

    /** @psalm-suppress PropertyNotSetInConstructor */
    private array $conditions;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private function __construct()
    {
        
    }

    public static function fromCondition(
        Kuery $kuery,
        Table $table,
        array $joins,
        array $columns,
        array $values,
        ConditionInterface $condition
    ): static
    {
        $object = new static;
        $object->kuery = $kuery;
        $object->table = $table;
        $object->joins = $joins;
        $object->updatableColumns = $columns;
        $object->updatableValues = $values;
        $object->conditions[] = $condition;

        return $object;
    }

    public function where(ConditionInterface $condition): self
    {
        $this->conditions[] = $condition;

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

        $sql .= 'SET ' . implode(', ', $parts) . PHP_EOL;

        if ($this->conditions !== []) {
            $sql .= 'WHERE ' . (implode(' AND ', array_map(fn(ConditionInterface $condition) => $condition->getSQL(), $this->conditions))) . ' ';
        }

        return trim($sql);
    }

    public function getBindValues(): array
    {
        $binds = array_filter($this->updatableValues, fn($value) => !($value instanceof SQLableInterface));

        foreach ($this->conditions as $condition) {
            $binds = array_merge($binds, $condition->getBindValues());
        }

        return $binds;
    }
}
