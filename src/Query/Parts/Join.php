<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Parts;

use Falgun\Typo\Conditions\Equal;
use Falgun\Typo\Interfaces\JoinInterface;
use Falgun\Typo\Conditions\ConditionInterface;
use Falgun\Typo\Interfaces\TableLikeInterface;

final class Join implements JoinInterface
{

    private const TYPE_DEFAULT = '';
    private const TYPE_INNER = 'INNER';
    private const TYPE_LEFT = 'LEFT';

    private string $type;
    private TableLikeInterface $table;
    private ConditionInterface $condition;

    /**
     *
     * @param self::TYPE_DEFAULT|self::TYPE_INNER|self::TYPE_LEFT $type
     * @param TableLikeInterface $table
     * @param ConditionInterface $condition
     */
    private function __construct(string $type, TableLikeInterface $table, ConditionInterface $condition)
    {
        $this->type = $type;
        $this->table = $table;
        $this->condition = $condition;
    }

    public static function new(TableLikeInterface $table): static
    {
        return new static(self::TYPE_DEFAULT, $table, Equal::fromSides(Literal::from(1), Literal::from(1)));
    }

    public function asInner(): JoinInterface
    {
        $join = clone $this;
        $join->type = self::TYPE_INNER;

        return $join;
    }

    public function asLeft(): JoinInterface
    {
        $leftJoin = clone $this;
        $leftJoin->type = self::TYPE_LEFT;

        return $leftJoin;
    }

    public function on(ConditionInterface $condition): JoinInterface
    {
        $this->condition = $condition;

        return $this;
    }

    public function getSQL(): string
    {
        return ($this->type ? $this->type . ' ' : '') .
            'JOIN ' . $this->table->getSQL() . ' ON ' . $this->condition->getSQL();
    }
}
