<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Delete;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Parts\Limit;
use Falgun\Typo\Query\Parts\Table;
use Falgun\Typo\Query\Parts\Collection;
use Falgun\Typo\Query\Parts\JoinInterface;
use Falgun\Typo\Query\Parts\OrderByInterface;
use Falgun\Typo\Query\Conditions\ConditionInterface;
use Falgun\Typo\Query\Parts\Condition\ConditionGroup;

final class DeleteQueryFinalStep
{

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Kuery $kuery;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Table $table;

    /**
     * @var array<int, JoinInterface>
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private array $joins;
    private ConditionGroup $conditionGroup;

    /**
     * @var array<int, OrderByInterface>
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private array $orderBys = [];

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Limit $limit;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private function __construct()
    {
        $this->conditionGroup = ConditionGroup::fromBlank();
        $this->limit = Limit::fromBlank();
    }

    /**
     * @param Kuery $kuery
     * @param Table $table
     * @param array<int, JoinInterface> $joins
     * @param ConditionGroup $conditionGroup
     * @param array<int, OrderByInterface> $orderBys
     * @param Limit $limit
     *
     * @return DeleteQueryFinalStep
     */
    public static function fromLastStep(
        Kuery $kuery,
        Table $table,
        array $joins = [],
        ConditionGroup $conditionGroup = null,
        array $orderBys = [],
        Limit $limit = null,
    ): DeleteQueryFinalStep
    {
        $object = new static;
        $object->kuery = $kuery;
        $object->table = $table;
        $object->joins = $joins;
        $object->conditionGroup = $conditionGroup ?? ConditionGroup::fromBlank();
        $object->orderBys = $orderBys;
        $object->limit = $limit ?? Limit::fromBlank();

        return $object;
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

        $sql .= $this->conditionGroup->getSQL();

        $sql .= Collection::from($this->orderBys, PHP_EOL . 'ORDER BY')
            ->join();

        $sql .= $this->limit->getSQL();

        return $sql;
    }

    public function getBindValues(): array
    {
        $binds = [];

        foreach ($this->joins as $join) {
            $binds = [...$binds, ...$join->getBindValues()];
        }

        $binds = [...$binds, ...$this->conditionGroup->getBindValues()];

        $binds = [...$binds, ...$this->limit->getBindValues()];

        return $binds;
    }
}
