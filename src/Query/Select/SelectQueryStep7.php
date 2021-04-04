<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Select;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Result;
use Falgun\Typo\Query\Parts\Limit;
use Falgun\Typo\Query\Parts\Column;
use Falgun\Typo\Query\Parts\OrderByInterface;
use Falgun\Typo\Query\Parts\TableLikeInterface;
use Falgun\Typo\Query\Parts\ColumnLikeInterface;
use Falgun\Typo\Query\Parts\Condition\ConditionGroup;

final class SelectQueryStep7 implements SelectQueryInterface
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
    private function __construct()
    {
        $this->conditionGroup = ConditionGroup::fromBlank();
    }

    public static function fromLastStep(
        Kuery $kuery,
        array $columns,
        TableLikeInterface $table,
        array $joins,
        ConditionGroup $conditionGroup,
        array $groupBys,
        array $orderBys,
    ): SelectQueryStep7
    {
        $object = new static;
        $object->kuery = $kuery;
        $object->selectedColumns = $columns;
        $object->table = $table;
        $object->joins = $joins;
        $object->conditionGroup = $conditionGroup;
        $object->groupBys = $groupBys;
        $object->orderBys = $orderBys;

        return $object;
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
        );
    }
}
