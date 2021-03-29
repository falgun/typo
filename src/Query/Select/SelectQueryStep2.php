<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Select;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Parts\Collection;
use Falgun\Typo\Interfaces\JoinInterface;
use Falgun\Typo\Interfaces\OrderByInterface;
use Falgun\Typo\Interfaces\SQLableInterface;
use Falgun\Typo\Interfaces\ConditionInterface;
use Falgun\Typo\Interfaces\TableLikeInterface;
use Falgun\Typo\Interfaces\ColumnLikeInterface;

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

    /** @psalm-suppress PropertyNotSetInConstructor */
    private array $conditions = [];

    /**
     * @var array<int, OrderByInterface>
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private array $orderBys = [];

    /** @psalm-suppress PropertyNotSetInConstructor */
    private int $offset;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private int $limit;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private function __construct()
    {
        
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
        if (empty($this->conditions)) {
            $this->conditions[] = $condition;
        } else {
            $this->conditions[] = $condition->asAnd();
        }

        return $this;
    }

    public function andWhere(ConditionInterface $condition): SelectQueryStep2
    {
        $this->conditions[] = $condition->asAnd();

        return $this;
    }

    public function orWhere(ConditionInterface $condition): SelectQueryStep2
    {
        $this->conditions[] = $condition->asOr();

        return $this;
    }

    public function orderBy(OrderByInterface $orderBy, OrderByInterface ...$orderBys): SelectQueryStep2
    {
        $this->orderBys = [...$this->orderBys, $orderBy, ... $orderBys];

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
        $sql = 'SELECT ';
        $sql .= $this->getColumnListAsSQL();
        $sql .= PHP_EOL . 'FROM ' . $this->table->getSQL();

        foreach ($this->joins as $join) {
            $sql .= PHP_EOL . $join->getSQL();
        }

        if ($this->conditions !== []) {
            $sql .= PHP_EOL .
                'WHERE ' .
                (implode(' ', array_map(fn(ConditionInterface $condition) => $condition->getSQL(), $this->conditions)));
        }

        if ($this->orderBys !== []) {
            $sql .= PHP_EOL .
                    Collection::from($this->orderBys, 'ORDER BY')
                    ->join();
        }

        if (isset($this->offset) && isset($this->limit)) {
            $sql .= PHP_EOL . 'LIMIT ' . $this->offset . ', ' . $this->limit;
        } elseif (isset($this->limit)) {
            $sql .= PHP_EOL . 'LIMIT ' . $this->limit;
        }

        return $sql;
    }

    private function getColumnListAsSQL(): string
    {
        // here be dragon
        return (implode(', ',
                array_map(function (ColumnLikeInterface $column): string {
                    if ($column instanceof SelectQueryStep2) {
                        return '(' . $column->getSQL() . ')';
                    }
                    return $column->getSQL();
                }, $this->selectedColumns)
        ));
    }

    public function getBindValues(): array
    {
        $binds = [];

        foreach ($this->selectedColumns as $column) {
            $binds = array_merge($binds, $column->getBindValues());
        }

        $binds = array_merge($binds, $this->table->getBindValues());

        foreach ($this->conditions as $condition) {
            $binds = array_merge($binds, $condition->getBindValues());
        }

        return $binds;
    }
}
