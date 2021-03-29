<?php
declare(strict_types=1);

namespace Falgun\Typo\Conditions;

use Falgun\Typo\Query\Parts\Collection;
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
     * @var array<int, ConditionInterface>
     */
    private array $siblings = [];

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

    public function and(ConditionInterface $condition): ConditionInterface
    {
        $this->siblings[] = $condition->asAnd();

        return $this;
    }

    public function or(ConditionInterface $condition): ConditionInterface
    {
        $this->siblings[] = $condition->asOr();

        return $this;
    }

    public final function getSQL(): string
    {
        $placeholderSQL = '? AND ?';

        $sql = ($this->type ? ($this->type . ' ') : '') . '(';

        if ($this->siblings === []) {
            return $sql . $this->getConditionSQL($this->sideA, $placeholderSQL) . ')';
        }

        return $sql . '(' . $this->getConditionSQL($this->sideA, $placeholderSQL) . ') ' .
            Collection::from($this->siblings, '')->join(' ') . ')';
    }

    protected function getConditionSQL(SQLableInterface $sideA, string $placeholderSQL): string
    {
        return $sideA->getSQL() . ' BETWEEN ' . $placeholderSQL;
    }

    public final function getBindValues(): array
    {
        $binds = [$this->sideB, $this->sideC];

        foreach ($this->siblings as $sibling) {
            $binds = [...$binds, ...$sibling->getBindValues()];
        }

        return $binds;
    }
}
