<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Delete;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Parts\Limit;
use Falgun\Typo\Query\Parts\Table;
use Falgun\Typo\Query\Parts\Collection;
use Falgun\Typo\Interfaces\OrderByInterface;
use Falgun\Typo\Interfaces\ConditionInterface;
use Falgun\Typo\Query\Parts\Condition\ConditionGroup;

final class DeleteQueryStep2
{

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Kuery $kuery;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Table $table;

    /** @psalm-suppress PropertyNotSetInConstructor */
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

    public static function fromCondition(
        Kuery $kuery,
        Table $table,
        array $joins,
        ConditionInterface $condition
    ): DeleteQueryStep2
    {
        $object = new static;
        $object->kuery = $kuery;
        $object->table = $table;
        $object->joins = $joins;
        $object->conditionGroup = ConditionGroup::fromFirstCondition($condition);

        return $object;
    }

    public function andWhere(ConditionInterface $condition): DeleteQueryStep2
    {
        $this->conditionGroup->and($condition);

        return $this;
    }

    public function orWhere(ConditionInterface $condition): DeleteQueryStep2
    {
        $this->conditionGroup->or($condition);

        return $this;
    }

    public function orderBy(OrderByInterface $orderBy, OrderByInterface ...$orderBys): DeleteQueryStep2
    {
        $this->orderBys = [...$this->orderBys, $orderBy, ...$orderBys];

        return $this;
    }

    public function limit(int $offsetOrLimit, ?int $limit = null): DeleteQueryStep2
    {
        $this->limit = Limit::fromOffsetLimit($offsetOrLimit, $limit);

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
