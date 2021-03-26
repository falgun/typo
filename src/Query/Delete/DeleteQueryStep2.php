<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Delete;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Parts\Table;
use Falgun\Typo\Query\Parts\Column;
use Falgun\Typo\Conditions\ConditionInterface;

final class DeleteQueryStep2
{

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Kuery $kuery;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Table $table;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private array $conditions;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private function __construct()
    {
        
    }

    public static function fromCondition(
        Kuery $kuery,
        Table $table,
        ConditionInterface $condition
    ): static
    {
        $object = new static;
        $object->kuery = $kuery;
        $object->table = $table;
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
        $sql = 'DELETE FROM ' . $this->table->getSQL() . PHP_EOL;

        if ($this->conditions !== []) {
            $sql .= 'WHERE ' . (implode(
                    ' AND ',
                    array_map(
                        fn(ConditionInterface $condition): string => $condition->getSQL(),
                        $this->conditions
                    )
            ));
        }

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
