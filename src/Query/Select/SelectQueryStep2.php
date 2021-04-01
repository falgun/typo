<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Select;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Parts\Column;
use Falgun\Typo\Query\Parts\Collection;
use Falgun\Typo\Interfaces\JoinInterface;
use Falgun\Typo\Interfaces\OrderByInterface;
use Falgun\Typo\Interfaces\SQLableInterface;
use Falgun\Typo\Interfaces\ConditionInterface;
use Falgun\Typo\Interfaces\TableLikeInterface;
use Falgun\Typo\Interfaces\ColumnLikeInterface;
use Falgun\Typo\Query\Parts\Condition\ConditionGroup;

final class SelectQueryStep2 implements SQLableInterface
{

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Kuery $kuery;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private array $selectedColumns;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private TableLikeInterface $table;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private array $joins = [];
    private ConditionGroup $conditionGroup;

    /**
     * @var array<int, OrderByInterface>
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private array $orderBys = [];

    /**
     * @var array<int, Column>
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private array $groupBys = [];

    /** @psalm-suppress PropertyNotSetInConstructor */
    private int $offset;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private int $limit;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private function __construct()
    {
        $this->conditionGroup = ConditionGroup::fromBlank();
    }

    public static function fromTable(Kuery $kuery, array $columns, TableLikeInterface $table): static
    {
        $object = new static;
        $object->kuery = $kuery;
        $object->selectedColumns = $columns;
        $object->table = $table;

        return $object;
    }

    public function join(JoinInterface $join): SelectQueryStep2
    {
        $this->joins[] = $join;

        return $this;
    }

    public function innerJoin(JoinInterface $join): SelectQueryStep2
    {
        $this->joins[] = $join->asInner();

        return $this;
    }

    public function leftJoin(JoinInterface $join): SelectQueryStep2
    {
        $this->joins[] = $join->asLeft();

        return $this;
    }

    public function where(ConditionInterface $condition): SelectQueryStep2
    {
        // intentionally making where() required before andWhere(), orWhere()
        $this->conditionGroup = ConditionGroup::fromFirstCondition($condition);

        return $this;
    }

    public function andWhere(ConditionInterface $condition): SelectQueryStep2
    {
        $this->conditionGroup->and($condition);

        return $this;
    }

    public function orWhere(ConditionInterface $condition): SelectQueryStep2
    {
        $this->conditionGroup->or($condition);

        return $this;
    }

    public function orderBy(OrderByInterface $orderBy, OrderByInterface ...$orderBys): SelectQueryStep2
    {
        $this->orderBys = [...$this->orderBys, $orderBy, ... $orderBys];

        return $this;
    }

    public function groupBy(Column $groupBy, Column ...$groupBys): SelectQueryStep2
    {
        $this->groupBys = [...$this->groupBys, $groupBy, ... $groupBys];

        return $this;
    }

    public function limit(int $offsetOrLimit, int $limit = null): SelectQueryStep2
    {
        if ($limit === null) {
            $this->limit = $offsetOrLimit;
        } else {
            $this->offset = $offsetOrLimit;
            $this->limit = $limit;
        }

        return $this;
    }

    public function fetch(): array
    {
        $stmt = $this->kuery->run($this->getSQL(), $this->getBindValues());

        return $this->kuery->fetchAllAsArray($stmt);
    }

    public function as(string $alias): ColumnLikeInterface
    {
        return SubQueryColumn::fromQuery($this)->as($alias);
    }

    public function asTable(string $alias): TableLikeInterface
    {
        return SubQueryTable::fromQuery($this)->as($alias);
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

        $sql .= Collection::from($this->orderBys, PHP_EOL . 'ORDER BY')
            ->join();

        if (isset($this->offset) && isset($this->limit)) {
            $sql .= PHP_EOL . 'LIMIT ' . $this->offset . ', ' . $this->limit;
        } elseif (isset($this->limit)) {
            $sql .= PHP_EOL . 'LIMIT ' . $this->limit;
        }

        return $sql;
    }

    public function getBindValues(): array
    {
        $binds = [];

        foreach ($this->selectedColumns as $column) {
            $binds = array_merge($binds, $column->getBindValues());
        }

        foreach ($this->joins as $join) {
            $binds = [...$binds, ...$join->getBindValues()];
        }

        $binds = array_merge($binds, $this->table->getBindValues());

        $binds = [...$binds, ...$this->conditionGroup->getBindValues()];

        return $binds;
    }
}
