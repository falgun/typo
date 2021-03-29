<?php

namespace Falgun\Typo\Interfaces;

use Falgun\Typo\Query\Parts\Column;

interface OrderByInterface extends SQLableInterface
{

    public static function asc(Column $column): OrderByInterface;

    public static function desc(Column $column): OrderByInterface;
}
