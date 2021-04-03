<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Delete;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Parts\Limit;
use Falgun\Typo\Query\Parts\Table;
use Falgun\Typo\Interfaces\OrderByInterface;
use Falgun\Typo\Query\Parts\Condition\ConditionGroup;

final class DeleteQueryStep3
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
    private function __construct()
    {
        $this->conditionGroup = ConditionGroup::fromBlank();
    }

    public static function fromOrderBy(
        Kuery $kuery,
        Table $table,
        array $joins,
        ConditionGroup $conditionGroup,
        array $orderBys,
    ): DeleteQueryStep3
    {
        $object = new static;
        $object->kuery = $kuery;
        $object->table = $table;
        $object->joins = $joins;
        $object->conditionGroup = $conditionGroup;
        $object->orderBys = $orderBys;

        return $object;
    }

    public function limit(int $offsetOrLimit, ?int $limit = null): DeleteQueryFinalStep
    {
        return DeleteQueryFinalStep::fromLastStep(
                $this->kuery,
                $this->table,
                $this->joins,
                $this->conditionGroup,
                $this->orderBys,
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
