<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Select;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Parts\Column;
use Falgun\Typo\Query\Parts\OrderBy;
use Falgun\Typo\Interfaces\JoinInterface;
use Falgun\Typo\Interfaces\SQLableInterface;
use Falgun\Typo\Conditions\ConditionInterface;
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

    /** @psalm-suppress PropertyNotSetInConstructor */
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

    public function orderBy(Column $column): SelectQueryStep2
    {
        $this->orderBys[] = OrderBy::asc($column);

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

    public function getSQL(): string
    {
        $sql = 'SELECT ';
        $sql .= (implode(', ', array_map(fn(ColumnLikeInterface $column) => $column->getSQL(), $this->selectedColumns))) . PHP_EOL;
        $sql .= 'FROM ' . $this->table->getSQL() . PHP_EOL;

        foreach ($this->joins as $join) {
            $sql .= $join->getSQL() . PHP_EOL;
        }

        if ($this->conditions !== []) {
            $sql .= 'WHERE ' . (implode(' ', array_map(fn(ConditionInterface $condition) => $condition->getSQL(), $this->conditions))) . PHP_EOL;
        }

        if ($this->orderBys !== []) {
            $sql .= 'ORDER BY ' . (implode(' ', array_map(fn(OrderBy $orderBy) => $orderBy->getSQL(), $this->orderBys))) . PHP_EOL;
        }

        if (isset($this->offset) && isset($this->limit)) {
            $sql .= 'LIMIT ' . $this->offset . ', ' . $this->limit;
        } elseif (isset($this->limit)) {
            $sql .= 'LIMIT ' . $this->limit;
        }

        if (isset($this->alias)) {
            return "({$sql}) as {$this->alias}";
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
