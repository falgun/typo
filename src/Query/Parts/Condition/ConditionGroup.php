<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Parts\Condition;

use Falgun\Typo\Query\SQLableInterface;
use Falgun\Typo\Conditions\ConditionInterface;

final class ConditionGroup implements SQLableInterface
{

    /**
     * @var array<int, ConditionDTO>
     */
    private array $conditions;

    /**
     * @param array<int, ConditionDTO> $conditions
     */
    private function __construct(array $conditions = [])
    {
        $this->conditions = $conditions;
    }

    public static function fromBlank(): ConditionGroup
    {
        return new static();
    }

    public static function fromFirstCondition(ConditionInterface $condition): ConditionGroup
    {
        return new static([ConditionDTO::where($condition)]);
    }

    public static function fromAndCondition(ConditionInterface $condition): ConditionGroup
    {
        return new static([ConditionDTO::and($condition)]);
    }

    public static function fromOrCondition(ConditionInterface $condition): ConditionGroup
    {
        return new static([ConditionDTO::or($condition)]);
    }

    public function and(ConditionInterface $condition): ConditionGroup
    {
        $this->conditions[] = ConditionDTO::and($condition);

        return $this;
    }

    public function or(ConditionInterface $condition): ConditionGroup
    {
        $this->conditions[] = ConditionDTO::or($condition);

        return $this;
    }

    public function hasConditions(): bool
    {
        return $this->conditions !== [];
    }

    public function getBindValues(): array
    {
        $binds = [];

        foreach ($this->conditions as $conditionDTO) {
            $binds = [...$binds, ...($conditionDTO->getBindValues())];
        }

        return $binds;
    }

    public function getSQL(): string
    {
        $sql = '';

        foreach ($this->conditions as $conditionDTO) {
            $sql .= $conditionDTO->getSQL();
        }

        return $sql;
    }
}
