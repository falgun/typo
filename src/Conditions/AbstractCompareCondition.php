<?php
declare(strict_types=1);

namespace Falgun\Typo\Conditions;

use Falgun\Typo\Interfaces\SQLableInterface;
use Falgun\Typo\Interfaces\SubQueryInterface;
use Falgun\Typo\Interfaces\ConditionInterface;

abstract class AbstractCompareCondition implements ConditionInterface
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
     * @var mixed
     */
    private $sideB;

    /**
     *
     * @param SQLableInterface $sideA
     * @param mixed $sideB
     */
    private final function __construct(SQLableInterface $sideA, $sideB)
    {
        $this->type = self::TYPE_DEFAULT;
        $this->sideA = $sideA;
        $this->sideB = $sideB;
    }

    /**
     *
     * @param SQLableInterface $sideA
     * @param mixed $sideB
     *
     * @return static
     */
    public final static function fromSides(SQLableInterface $sideA, $sideB): static
    {
        return new static($sideA, $sideB);
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
        if (is_object($this->sideB) && $this->sideB instanceof SQLableInterface) {
            $placeholderSQL = $this->sideB->getSQL();
        } else {
            $placeholderSQL = '?';
        }

        return ($this->type ? ($this->type . ' ') : '') .
            $this->getConditionSQL($this->sideA, $placeholderSQL);
    }

    protected abstract function getConditionSQL(SQLableInterface $sideA, string $placeholderSQL): string;

    public final function getBindValues(): array
    {
        if (is_object($this->sideB) && $this->sideB instanceof SubQueryInterface) {
            // subquery
            return $this->sideB->getBindValues();
        } elseif (is_object($this->sideB) && $this->sideB instanceof SQLableInterface) {
            // column
            return [];
        }

        return [$this->sideB];
    }
}
