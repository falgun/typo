<?php
declare(strict_types=1);

namespace Falgun\Typo\Conditions;

use Falgun\Typo\Query\Parts\Collection;
use Falgun\Typo\Interfaces\SQLableInterface;
use Falgun\Typo\Interfaces\ConditionInterface;
use Falgun\Typo\Query\Parts\Condition\ConditionGroup;

class Between implements ConditionInterface
{

    private SQLableInterface $sideA;

    /**
     *
     * @var string|int|float
     */
    private $sideB;

    /**
     *
     * @var string|int|float
     */
    private $sideC;
    private ConditionGroup $siblings;

    /**
     *
     * @param SQLableInterface $sideA
     * @param string|int|float $sideB
     * @param string|int|float $sideC
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
     * @param string|int|float $sideB
     * @param string|int|float $sideC
     *
     * @return static
     */
    public final static function fromSides(SQLableInterface $sideA, string|int|float $sideB, string|int|float $sideC): static
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
        $placeholderSQL = '? AND ?';

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
        $binds = [$this->sideB, $this->sideC];

        if ($this->siblings->hasConditions()) {
            $binds = [...$binds, ...$this->siblings->getBindValues()];
        }

        return $binds;
    }
}
