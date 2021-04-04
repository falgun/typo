<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Select;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Result;
use Falgun\Typo\Query\Parts\Limit;
use Falgun\Typo\Query\Parts\Column;
use Falgun\Typo\Query\Parts\Collection;
use Falgun\Typo\Query\Parts\JoinInterface;
use Falgun\Typo\Query\Parts\OrderByInterface;
use Falgun\Typo\Query\Parts\TableLikeInterface;
use Falgun\Typo\Query\Parts\ColumnLikeInterface;
use Falgun\Typo\Query\Conditions\ConditionInterface;
use Falgun\Typo\Query\Parts\Condition\ConditionGroup;

final class SelectQueryFinalStep implements SelectQueryInterface
{

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Kuery $kuery;

    /**
     * @var array<int, ColumnLikeInterface>
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private array $selectedColumns;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private TableLikeInterface $table;

    /**
     * @var array<int, JoinInterface>
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private array $joins = [];
    private ConditionGroup $conditionGroup;

    /**
     * @var array<int, Column>
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private array $groupBys = [];
    private ?ConditionInterface $having;

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
        $this->having = null;
        $this->limit = Limit::fromBlank();
    }

    /**
     * @param Kuery $kuery
     * @param array<int, ColumnLikeInterface> $columns
     * @param TableLikeInterface $table
     * @param array<int, JoinInterface> $joins
     * @param ConditionGroup $conditionGroup
     * @param array<int, Column> $groupBys
     * @param ConditionInterface $having
     * @param array<int, OrderByInterface> $orderBys
     * @param Limit $limit
     *
     * @return SelectQueryFinalStep
     */
    public static function fromLastStep(
        Kuery $kuery,
        array $columns,
        TableLikeInterface $table,
        array $joins = [],
        ConditionGroup $conditionGroup = null,
        array $groupBys = [],
        ConditionInterface $having = null,
        array $orderBys = [],
        Limit $limit = null,
    ): SelectQueryFinalStep
    {
        $object = new static;
        $object->kuery = $kuery;
        $object->selectedColumns = $columns;
        $object->table = $table;
        $object->joins = $joins;
        $object->conditionGroup = $conditionGroup ?? ConditionGroup::fromBlank();
        $object->groupBys = $groupBys;
        $object->having = $having;
        $object->orderBys = $orderBys;
        $object->limit = $limit ?? Limit::fromBlank();

        return $object;
    }

    public function as(string $alias): ColumnLikeInterface
    {
        return SubQueryColumn::fromQuery($this)->as($alias);
    }

    public function asTable(string $alias): TableLikeInterface
    {
        return SubQueryTable::fromQuery($this)->as($alias);
    }

    public function fetch(): Result
    {
        $stmt = $this->kuery->run($this->getSQL(), $this->getBindValues());

        return Result::fromStmtKuery($stmt, $this->kuery);
    }

    public function getSQL(): string
    {
        $sql = Collection::from($this->selectedColumns, 'SELECT')
                ->join() . PHP_EOL;
        $sql .= 'FROM ' . $this->table->getSQL();

        foreach ($this->joins as $join) {
            $sql .= PHP_EOL . $join->getSQL();
        }

        $sql .= $this->conditionGroup->getSQL();

        $sql .= Collection::from($this->groupBys, PHP_EOL . 'GROUP BY')
            ->join();

        if (isset($this->having)) {
            $sql .= PHP_EOL . 'HAVING ' . $this->having->getSQL();
        }

        $sql .= Collection::from($this->orderBys, PHP_EOL . 'ORDER BY')
            ->join();

        $sql .= $this->limit->getSQL();

        return $sql;
    }

    public function getBindValues(): array
    {
        $binds = [];

        foreach ($this->selectedColumns as $column) {
            $binds = [...$binds, ...$column->getBindValues()];
        }

        foreach ($this->joins as $join) {
            $binds = [...$binds, ...$join->getBindValues()];
        }

        $binds = [...$binds, ...$this->table->getBindValues()];

        $binds = [...$binds, ...$this->conditionGroup->getBindValues()];

        if (isset($this->having)) {
            $binds = [...$binds, ...$this->having->getBindValues()];
        }

        $binds = [...$binds, ...$this->limit->getBindValues()];

        return $binds;
    }
}
