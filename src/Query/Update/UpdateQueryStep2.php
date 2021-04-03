<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Update;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Parts\Table;
use Falgun\Typo\Query\Parts\Column;
use Falgun\Typo\Interfaces\SQLableInterface;
use Falgun\Typo\Interfaces\ConditionInterface;
use Falgun\Typo\Query\Update\UpdateQueryFinalStep;
use Falgun\Typo\Query\Parts\Condition\ConditionGroup;

final class UpdateQueryStep2 implements SQLableInterface
{

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Kuery $kuery;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Table $table;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private array $joins;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private array $updatableColumns;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private array $updatableValues;
    private ConditionGroup $conditionGroup;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private function __construct()
    {
        $this->conditionGroup = ConditionGroup::fromBlank();
    }

    /**
     * @param Kuery $kuery
     * @param Table $table
     * @param array $joins
     * @param Column $column
     * @param mixed $value
     *
     * @return UpdateQueryStep2
     */
    public static function fromStep1(
        Kuery $kuery,
        Table $table,
        array $joins,
        Column $column,
        $value
    ): UpdateQueryStep2
    {
        $object = new static;
        $object->kuery = $kuery;
        $object->table = $table;
        $object->joins = $joins;
        $object->set($column, $value);

        return $object;
    }

    /**
     *
     * @param Column $column
     * @param mixed $value
     *
     * @return UpdateQueryStep2
     */
    public function set(Column $column, $value): UpdateQueryStep2
    {
        $this->updatableColumns[] = $column;
        $this->updatableValues[] = $value;

        return $this;
    }

    public function where(ConditionInterface $condition): UpdateQueryFinalStep
    {
        return UpdateQueryFinalStep::fromStep2(
                $this->kuery,
                $this->table,
                $this->joins,
                $this->updatableColumns,
                $this->updatableValues,
                $condition,
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

    private function getFinalStep(): UpdateQueryFinalStep
    {
        return UpdateQueryFinalStep::fromStep2(
                $this->kuery,
                $this->table,
                $this->joins,
                $this->updatableColumns,
                $this->updatableValues,
        );
    }
}
