<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Parts;

use Falgun\Typo\Interfaces\OrderByInterface;

final class OrderBy implements OrderByInterface
{

    const BY_ASC = 'ASC';
    const BY_DESC = 'DESC';

    private Column $column;

    /** @var self::BY_ASC|self::BY_DESC */
    private string $sortDirection;

    /**
     * @param Column $column
     * @param self::BY_ASC|self::BY_DESC $sortDirection
     */
    private function __construct(Column $column, string $sortDirection)
    {
        $this->column = $column;
        $this->sortDirection = (
            $sortDirection === self::BY_ASC ? self::BY_ASC : self::BY_DESC
            );
    }

    public static function asc(Column $column): OrderByInterface
    {
        return new static($column, self::BY_ASC);
    }

    public static function desc(Column $column): OrderByInterface
    {
        return new static($column, self::BY_DESC);
    }

    public function getSQL(): string
    {
        return $this->column->getSQL() . ' ' . $this->sortDirection;
    }

    public function getBindValues(): array
    {
        return [];
    }
}
