<?php
declare(strict_types=1);

namespace Falgun\Typo\Conditions;

use Falgun\Typo\Interfaces\SQLableInterface;
use Falgun\Typo\Interfaces\ConditionInterface;
use Falgun\Typo\Interfaces\ColumnLikeInterface;
use Falgun\Typo\Query\Parts\Condition\ConditionGroup;

class Between implements ConditionInterface
{

    private SQLableInterface $sideA;

    /**
     *
     * @var string|int|float|ColumnLikeInterface
     */
    private $sideB;

    /**
     *
     * @var string|int|float|ColumnLikeInterface
     */
    private $sideC;
    private ConditionGroup $siblings;

    /**
     *
     * @param SQLableInterface $sideA
     * @param string|int|float|ColumnLikeInterface $sideB
     * @param string|int|float|ColumnLikeInterface $sideC
     */
    private final function __construct(SQLableInterface $sideA, $sideB, $sideC)
    {
        $this->sideA = $sideA;
        $this->sideB = $sideB;
        $this->sideC = $sideC;
        $this->siblings = ConditionGroup::fromBlank();
    }

    /**
     *
     * @param SQLableInterface $sideA
     * @param string|int|float|ColumnLikeInterface $sideB
     * @param string|int|float|ColumnLikeInterface $sideC
     *
     * @return static
     */
    public final static function fromSides(
        SQLableInterface $sideA,
        string|int|float|ColumnLikeInterface $sideB,
        string|int|float|ColumnLikeInterface $sideC,
    ): static
    {
        return new static($sideA, $sideB, $sideC);
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

    public final function getSQL(): string
    {
        $placeholderSQL = '';

        if (is_object($this->sideB)) {
            $placeholderSQL .= $this->sideB->getSQL();
        } else {
            $placeholderSQL .= '?';
        }

        $placeholderSQL .= ' AND ';

        if (is_object($this->sideC)) {
            $placeholderSQL .= $this->sideC->getSQL();
        } else {
            $placeholderSQL .= '?';
        }

        $sql = '(';

        if ($this->siblings->hasConditions() === false) {
            return $sql . $this->getConditionSQL($this->sideA, $placeholderSQL) . ')';
        }

        return $sql . '(' . $this->getConditionSQL($this->sideA, $placeholderSQL) . ')' .
            $this->siblings->getSQL() . ')';
    }

    protected function getConditionSQL(SQLableInterface $sideA, string $placeholderSQL): string
    {
        return $sideA->getSQL() . ' BETWEEN ' . $placeholderSQL;
    }

    public final function getBindValues(): array
    {
        $binds = [];

        if (is_object($this->sideB)) {
            $binds = [...$binds, ...$this->sideB->getBindValues()];
        } else {
            $binds[] = $this->sideB;
        }

        if (is_object($this->sideC)) {
            $binds = [...$binds, ...$this->sideC->getBindValues()];
        } else {
            $binds[] = $this->sideC;
        }

        if ($this->siblings->hasConditions()) {
            $binds = [...$binds, ...$this->siblings->getBindValues()];
        }

        return $binds;
    }
}
