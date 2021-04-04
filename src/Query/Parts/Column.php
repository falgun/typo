<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Parts;

use Falgun\Typo\Query\Conditions\In;
use Falgun\Typo\Query\Conditions\Like;
use Falgun\Typo\Query\Conditions\NotIn;
use Falgun\Typo\Query\Conditions\Equal;
use Falgun\Typo\Query\Conditions\IsNull;
use Falgun\Typo\Query\Conditions\NotLike;
use Falgun\Typo\Query\Conditions\Between;
use Falgun\Typo\Query\Parts\Literal;
use Falgun\Typo\Query\Parts\OrderBy;
use Falgun\Typo\Query\Conditions\NotEqual;
use Falgun\Typo\Query\Conditions\IsNotNull;
use Falgun\Typo\Query\Conditions\NotBetween;
use Falgun\Typo\Query\Conditions\LesserThan;
use Falgun\Typo\Query\Conditions\GreaterThan;
use Falgun\Typo\Query\Conditions\LesserThanEqual;
use Falgun\Typo\Query\Conditions\GreaterThanEqual;
use Falgun\Typo\Query\Conditions\ConditionInterface;

final class Column implements ColumnLikeInterface
{

    private string $name;
    private string $alias;
    private string $type;
    private bool $nullable;
    private string $key;
    private string $default;
    private string $extra;

    private function __construct(
        string $name,
        string $alias,
        string $type,
        bool $nullable,
        string $key,
        string $default,
        string $extra
    )
    {
        $this->name = $name;
        $this->alias = $alias;
        $this->type = $type;
        $this->nullable = $nullable;
        $this->key = $key;
        $this->default = $default;
        $this->extra = $extra;
    }

    public static function fromSchema(
        string $name,
        string $type = 'varchar',
        bool $nullable = false,
        string $key = '',
        string $default = '',
        string $extra = ''
    ): static
    {
        return new static($name, $name, $type, $nullable, $key, $default, $extra);
    }

    public function as(string $alias): ColumnLikeInterface
    {
        $column = clone $this;

        $column->alias = $alias;

        return $column;
    }

    public function getSQL(): string
    {
        if ($this->name === $this->alias) {
            return $this->name;
        }

        return $this->name . ' as ' . $this->alias;
    }

    public function getBindValues(): array
    {
        return [];
    }

    /**
     * @param mixed $value
     *
     * @return ConditionInterface
     */
    public function eq($value): ConditionInterface
    {
        return Equal::fromSides($this, $value);
    }

    /**
     * @param mixed $value
     *
     * @return ConditionInterface
     */
    public function neq($value): ConditionInterface
    {
        return NotEqual::fromSides($this, $value);
    }

    /**
     * @param mixed $value
     *
     * @return ConditionInterface
     */
    public function gt($value): ConditionInterface
    {
        return GreaterThan::fromSides($this, $value);
    }

    /**
     * @param mixed $value
     *
     * @return ConditionInterface
     */
    public function gte($value): ConditionInterface
    {
        return GreaterThanEqual::fromSides($this, $value);
    }

    /**
     * @param mixed $value
     *
     * @return ConditionInterface
     */
    public function lt($value): ConditionInterface
    {
        return LesserThan::fromSides($this, $value);
    }

    /**
     * @param mixed $value
     *
     * @return ConditionInterface
     */
    public function lte($value): ConditionInterface
    {
        return LesserThanEqual::fromSides($this, $value);
    }

    /**
     * @param mixed $value
     *
     * @return ConditionInterface
     */
    public function in($value): ConditionInterface
    {
        return In::fromSides($this, $value);
    }

    /**
     * @param mixed $value
     *
     * @return ConditionInterface
     */
    public function notIn($value): ConditionInterface
    {
        return NotIn::fromSides($this, $value);
    }

    /**
     * @param string|int|float|ColumnLikeInterface $valueA
     * @param string|int|float|ColumnLikeInterface $valueB
     *
     * @return ConditionInterface
     */
    public function between(
        string|int|float|ColumnLikeInterface $valueA,
        string|int|float|ColumnLikeInterface $valueB,
    ): ConditionInterface
    {
        return Between::fromSides($this, $valueA, $valueB);
    }

    /**
     * @param string|int|float|ColumnLikeInterface $valueA
     * @param string|int|float|ColumnLikeInterface $valueB
     *
     * @return ConditionInterface
     */
    public function notBetween(
        string|int|float|ColumnLikeInterface $valueA,
        string|int|float|ColumnLikeInterface $valueB,
    ): ConditionInterface
    {
        return NotBetween::fromSides($this, $valueA, $valueB);
    }

    public function isNull(): ConditionInterface
    {
        return IsNull::fromSides($this, Literal::from(''));
    }

    public function isNotNull(): ConditionInterface
    {
        return IsNotNull::fromSides($this, Literal::from(''));
    }

    public function like(string $value): ConditionInterface
    {
        return Like::fromSides($this, $value);
    }

    public function notLike(string $value): ConditionInterface
    {
        return NotLike::fromSides($this, $value);
    }

    public function asc(): OrderByInterface
    {
        return OrderBy::asc($this);
    }

    public function desc(): OrderByInterface
    {
        return OrderBy::desc($this);
    }
}
