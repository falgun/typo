<?php
declare(strict_types=1);

namespace Falgun\Typo\Conditions;

use Falgun\Typo\Interfaces\SQLableInterface;
use Falgun\Typo\Interfaces\ConditionInterface;

class Between implements ConditionInterface
{

    private const TYPE_DEFAULT = '';
    private const TYPE_AND = 'AND';
    private const TYPE_OR = 'OR';

    /**
     * @var self::TYPE_DEFAULT|self::TYPE_AND|self::TYPE_OR $type
     */
    private string $type;
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

    /**
     *
     * @param SQLableInterface $sideA
     * @param string|int|float $sideB
     * @param string|int|float $sideC
     */
    private final function __construct(SQLableInterface $sideA, $sideB, $sideC)
    {
        $this->type = self::TYPE_DEFAULT;
        $this->sideA = $sideA;
        $this->sideB = $sideB;
        $this->sideC = $sideC;
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

    public final function asAnd(): ConditionInterface
    {
        $orCondition = clone $this;

        $orCondition->type = self::TYPE_AND;

        return $orCondition;
    }

    public final function asOr(): ConditionInterface
    {
        $orCondition = clone $this;

        $orCondition->type = self::TYPE_OR;

        return $orCondition;
    }

    public final function getSQL(): string
    {
        $placeholderSQL = '? AND ?';

        return '(' .
            ($this->type ? ($this->type . ' ') : '') .
            $this->getConditionSQL($this->sideA, $placeholderSQL) .
            ')';
    }

    protected function getConditionSQL(SQLableInterface $sideA, string $placeholderSQL): string
    {
        return $sideA->getSQL() . ' BETWEEN ' . $placeholderSQL;
    }

    public final function getBindValues(): array
    {
        return [$this->sideB, $this->sideC];
    }
}
