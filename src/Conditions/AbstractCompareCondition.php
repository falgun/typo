<?php
declare(strict_types=1);

namespace Falgun\Typo\Conditions;

use Falgun\Typo\Query\SQLableInterface;
use Falgun\Typo\Query\SubQueryInterface;
use Falgun\Typo\Query\Parts\Condition\ConditionGroup;

abstract class AbstractCompareCondition implements ConditionInterface
{

    private SQLableInterface $sideA;

    /**
     *
     * @var mixed
     */
    private $sideB;
    private ConditionGroup $siblings;

    /**
     *
     * @param SQLableInterface $sideA
     * @param mixed $sideB
     */
    final private function __construct(SQLableInterface $sideA, $sideB)
    {
        $this->sideA = $sideA;
        $this->sideB = $sideB;
        $this->siblings = ConditionGroup::fromBlank();
    }

    /**
     *
     * @param SQLableInterface $sideA
     * @param mixed $sideB
     *
     * @return static
     */
    final public static function fromSides(SQLableInterface $sideA, $sideB): static
    {
        return new static($sideA, $sideB);
    }

    public function and(ConditionInterface $condition): ConditionInterface
    {
        $this->siblings->and($condition);

        return $this;
    }

    public function or(ConditionInterface $condition): ConditionInterface
    {
        $this->siblings->or($condition);

        return $this;
    }

    final public function getSQL(): string
    {
        if (is_object($this->sideB) && $this->sideB instanceof SQLableInterface) {
            $placeholderSQL = $this->sideB->getSQL();
        } else {
            $placeholderSQL = $this->prepareValuePlaceholder($this->sideB);
        }

        $sql = '';

        if ($this->siblings->hasConditions() === false) {
            return $sql . $this->getConditionSQL($this->sideA, $placeholderSQL);
        }

        return $sql . '(' . $this->getConditionSQL($this->sideA, $placeholderSQL) .
            $this->siblings->getSQL() . ')';
    }

    /**
     * @param mixed $sideB
     *
     * @return string
     */
    protected function prepareValuePlaceholder($sideB): string
    {
        return '?';
    }

    abstract protected function getConditionSQL(SQLableInterface $sideA, string $placeholderSQL): string;

    final public function getBindValues(): array
    {
        if (is_object($this->sideB) && $this->sideB instanceof SubQueryInterface) {
            // subquery
            $binds = $this->sideB->getBindValues();
        } elseif (is_object($this->sideB) && $this->sideB instanceof SQLableInterface) {
            // column
            $binds = [];
        } else {
            // user provided scaler input
            $binds = $this->prepareBindValues($this->sideB);
        }

        if ($this->siblings->hasConditions()) {
            $binds = [...$binds, ...$this->siblings->getBindValues()];
        }

        return $binds;
    }

    /**
     * @param mixed $sideB
     *
     * @return array
     */
    protected function prepareBindValues($sideB): array
    {
        return [$sideB];
    }
}
