<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Parts;

final class OrderBy
{

    const BY_ASC = 'ASC';
    const BY_DESC = 'DESC';

    private Column $column;
    private string $sortDirection;

    private function __construct(Column $column, string $sortDirection)
    {
        $this->column = $column;
        $this->sortDirection = $sortDirection === self::BY_ASC ? self::BY_ASC : self::BY_DESC;
    }

    public static function asc(Column $column): static
    {
        return new static($column, self::BY_ASC);
    }

    public static function desc(Column $column): static
    {
        return new static($column, self::BY_DESC);
    }

    public function getSQL(): string
    {
        return $this->column->getSQL() . ' ' . $this->sortDirection;
    }
}
