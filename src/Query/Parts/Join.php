<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Parts;

use Falgun\Typo\Query\Conditions\ConditionInterface;

final class Join implements JoinInterface
{

    private const TYPE_DEFAULT = '';
    private const TYPE_INNER = 'INNER';
    private const TYPE_LEFT = 'LEFT';

    private string $type;
    private TableLikeInterface $table;
    private ?ConditionInterface $condition;
    private ?ColumnLikeInterface $usingColumn;

    /**
     *
     * @param self::TYPE_DEFAULT|self::TYPE_INNER|self::TYPE_LEFT $type
     * @param TableLikeInterface $table
     */
    private function __construct(string $type, TableLikeInterface $table)
    {
        $this->type = $type;
        $this->table = $table;
        $this->condition = null;
        $this->usingColumn = null;
    }

    public static function new(TableLikeInterface $table): static
    {
        return new static(self::TYPE_DEFAULT, $table);
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

    public function using(ColumnLikeInterface $column): JoinInterface
    {
        $this->usingColumn = $column;

        return $this;
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    public function getSQL(): string
    {
        $sql = ($this->type ? $this->type . ' ' : '') .
            'JOIN ' . $this->table->getSQL();

        if (isset($this->condition)) {
            return $sql . ' ON ' . $this->condition->getSQL();
        } elseif (isset($this->usingColumn)) {
            // nasty explode right here :p
            return $sql . ' USING (' . explode('.', $this->usingColumn->getSQL())[1] . ')';
        }

        throw new \RuntimeException('JOIN must have atleast one condition or USING()');
    }

    public function getBindValues(): array
    {
        if (isset($this->condition)) {
            return $this->condition->getBindValues();
        }

        return [];
    }
}
