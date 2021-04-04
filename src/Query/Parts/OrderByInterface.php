<?php

namespace Falgun\Typo\Query\Parts;

use Falgun\Typo\Query\SQLableInterface;

interface OrderByInterface extends SQLableInterface
{

    public static function asc(Column $column): OrderByInterface;

    public static function desc(Column $column): OrderByInterface;
}
