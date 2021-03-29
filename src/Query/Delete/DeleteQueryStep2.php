<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Delete;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Parts\Table;
use Falgun\Typo\Query\Parts\Collection;
use Falgun\Typo\Interfaces\ConditionInterface;

final class DeleteQueryStep2
{

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Kuery $kuery;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Table $table;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private array $joins;

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
        ConditionInterface $condition
    ): static
    {
        $object = new static;
        $object->kuery = $kuery;
        $object->table = $table;
        $object->joins = $joins;
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
        $sql = 'DELETE FROM ' . $this->table->getSQL();

        foreach ($this->joins as $join) {
            $sql .= PHP_EOL . $join->getSQL();
        }

        $sql .= Collection::from($this->conditions, PHP_EOL . 'WHERE')
            ->join(' ');

        return $sql;
    }

    public function getBindValues(): array
    {
        $binds = [];

        foreach ($this->conditions as $condition) {
            $binds = array_merge($binds, $condition->getBindValues());
        }

        return $binds;
    }
}
