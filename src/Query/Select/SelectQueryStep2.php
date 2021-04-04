<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Select;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Result;
use Falgun\Typo\Query\Parts\Limit;
use Falgun\Typo\Query\Parts\Column;
use Falgun\Typo\Query\Parts\JoinInterface;
use Falgun\Typo\Query\Parts\OrderByInterface;
use Falgun\Typo\Query\Parts\TableLikeInterface;
use Falgun\Typo\Query\Parts\ColumnLikeInterface;
use Falgun\Typo\Query\Conditions\ConditionInterface;
use Falgun\Typo\Query\Parts\Condition\ConditionGroup;

final class SelectQueryStep2 implements SelectQueryInterface
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
     * @param array<int, ColumnLikeInterface> $columns
     * @param TableLikeInterface $table
     *
     * @return static
     */
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

    /**
     * @psalm-suppress DuplicateArrayKey
     */
    public function groupBy(Column $groupBy, Column ...$groupBys): SelectQueryStep2
    {
        $this->groupBys = [...$this->groupBys, $groupBy, ... $groupBys];

        return $this;
    }

    /**
     * @psalm-suppress DuplicateArrayKey
     */
    public function orderBy(OrderByInterface $orderBy, OrderByInterface ...$orderBys): SelectQueryStep2
    {
        $this->orderBys = [...$this->orderBys, $orderBy, ...$orderBys];

        return $this;
    }

    public function limit(int $offsetOrLimit, ?int $limit = null): SelectQueryFinalStep
    {
        return SelectQueryFinalStep::fromLastStep(
                $this->kuery,
                $this->selectedColumns,
                $this->table,
                $this->joins,
                $this->conditionGroup,
                $this->groupBys,
                $this->orderBys,
                Limit::fromOffsetLimit($offsetOrLimit, $limit),
        );
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
        return $this->getFinalStep()
                ->fetch();
    }

    public function getSQL(): string
    {
        return $this->getFinalStep()
                ->getSQL();
    }

    public function getBindValues(): array
    {
        return $this->getFinalStep()
                ->getBindValues();
    }

    private function getFinalStep(): SelectQueryFinalStep
    {
        return SelectQueryFinalStep::fromLastStep(
                $this->kuery,
                $this->selectedColumns,
                $this->table,
                $this->joins,
                $this->conditionGroup,
                $this->groupBys,
                $this->orderBys,
                $this->limit
        );
    }
}
