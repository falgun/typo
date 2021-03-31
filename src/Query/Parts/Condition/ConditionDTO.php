<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Parts\Condition;

use Falgun\Typo\Interfaces\ConditionInterface;
use Falgun\Typo\Interfaces\SQLableInterface;

final class ConditionDTO implements SQLableInterface
{

    private const TYPE_DEFAULT = 'WHERE';
    private const TYPE_AND = 'AND';
    private const TYPE_OR = 'OR';

    /**
     * @var self::TYPE_DEFAULT|self::TYPE_AND|self::TYPE_OR $type
     */
    private string $type;
    private ConditionInterface $condition;

    /**
     * 
     * @param self::TYPE_DEFAULT|self::TYPE_AND|self::TYPE_OR $type
     * @param ConditionInterface $condition
     */
    private function __construct(string $type, ConditionInterface $condition)
    {
        $this->type = $type;
        $this->condition = $condition;
    }

    public static function where(ConditionInterface $condition): ConditionDTO
    {
        return new static(self::TYPE_DEFAULT, $condition);
    }

    public static function and(ConditionInterface $condition): ConditionDTO
    {
        return new static(self::TYPE_AND, $condition);
    }

    public static function or(ConditionInterface $condition): ConditionDTO
    {
        return new static(self::TYPE_OR, $condition);
    }

    public function getBindValues(): array
    {
        return $this->condition->getBindValues();
    }

    public function getSQL(): string
    {
        return (($this->type === self::TYPE_DEFAULT) ? PHP_EOL : ' ') .
            $this->type . ' ' .
            $this->condition->getSQL();
    }
}
