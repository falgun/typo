<?php
declare(strict_types=1);

namespace Falgun\Typo\Conditions;

use Falgun\Typo\Interfaces\SQLableInterface;

final class Equal implements ConditionInterface
{

    private const TYPE_DEFAULT = '';
    private const TYPE_AND = 'AND';
    private const TYPE_OR = 'OR';

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
    private function __construct(SQLableInterface $sideA, $sideB)
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
    public static function fromSides(SQLableInterface $sideA, $sideB): static
    {
        return new static($sideA, $sideB);
    }

    public function asAnd(): ConditionInterface
    {
        $orCondition = clone $this;

        $orCondition->type = self::TYPE_AND;

        return $orCondition;
    }

    public function asOr(): ConditionInterface
    {
        $orCondition = clone $this;

        $orCondition->type = self::TYPE_OR;

        return $orCondition;
    }

    public function getSQL(): string
    {
        if (is_object($this->sideB) && $this->sideB instanceof SQLableInterface) {
            $placeholderSQL = $this->sideB->getSQL();
        } else {
            $placeholderSQL = '?';
        }

        return ($this->type ? ($this->type . ' ') : '')
            . $this->sideA->getSQL() . ' = ' . $placeholderSQL;
    }

    public function getBindValues(): array
    {
        if (is_object($this->sideB) && $this->sideB instanceof SQLableInterface) {
            return [];
        }

        return [$this->sideB];
    }
}
