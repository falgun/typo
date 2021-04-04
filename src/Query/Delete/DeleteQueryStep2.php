<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Delete;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Parts\Limit;
use Falgun\Typo\Query\Parts\Table;
use Falgun\Typo\Query\Parts\JoinInterface;
use Falgun\Typo\Query\Parts\OrderByInterface;
use Falgun\Typo\Query\Conditions\ConditionInterface;
use Falgun\Typo\Query\Parts\Condition\ConditionGroup;

final class DeleteQueryStep2
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
    private function __construct()
    {
        $this->conditionGroup = ConditionGroup::fromBlank();
    }

    /**
     * @param Kuery $kuery
     * @param Table $table
     * @param array<int, JoinInterface> $joins
     * @param ConditionInterface $condition
     *
     * @return DeleteQueryStep2
     */
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

    /**
     * @psalm-suppress DuplicateArrayKey
     */
    public function orderBy(OrderByInterface $orderBy, OrderByInterface ...$orderBys): DeleteQueryStep3
    {
        return DeleteQueryStep3::fromOrderBy(
                $this->kuery,
                $this->table,
                $this->joins,
                $this->conditionGroup,
                [...$this->orderBys, $orderBy, ...$orderBys],
        );
    }

    public function limit(int $offsetOrLimit, ?int $limit = null): DeleteQueryFinalStep
    {
        return DeleteQueryFinalStep::fromLastStep(
                $this->kuery,
                $this->table,
                $this->joins,
                $this->conditionGroup,
                [],
                Limit::fromOffsetLimit($offsetOrLimit, $limit),
        );
    }

    public function execute(): int
    {
        return $this->getFinalStep()
                ->execute();
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

    private function getFinalStep(): DeleteQueryFinalStep
    {
        return DeleteQueryFinalStep::fromLastStep(
                $this->kuery,
                $this->table,
                $this->joins,
                $this->conditionGroup,
                $this->orderBys,
                Limit::fromBlank(),
        );
    }
}
