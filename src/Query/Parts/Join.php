<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Parts;

use Falgun\Typo\Conditions\Equal;
use Falgun\Typo\Interfaces\JoinInterface;
use Falgun\Typo\Interfaces\SQLableInterface;
use Falgun\Typo\Conditions\ConditionInterface;
use Falgun\Typo\Interfaces\TableLikeInterface;

final class Join implements JoinInterface, SQLableInterface
{

    private string $type;
    private TableLikeInterface $table;
    private ConditionInterface $condition;

    private function __construct(string $type, TableLikeInterface $table, ConditionInterface $condition)
    {
        $this->type = $type;
        $this->table = $table;
        $this->condition = $condition;
    }

    public static function new(TableLikeInterface $table): static
    {
        return new static('INNER', $table, Equal::fromSides(Literal::from(1), Literal::from(1)));
    }

    public function asLeft(): JoinInterface
    {
        $leftJoin = clone $this;
        $leftJoin->type = 'LEFT';

        return $leftJoin;
    }

    public function on(ConditionInterface $condition): JoinInterface
    {
        $this->condition = $condition;

        return $this;
    }

    public function getSQL(): string
    {
        return $this->type . ' JOIN ' . $this->table->getSQL() . ' ON ' . $this->condition->getSQL();
    }
}
