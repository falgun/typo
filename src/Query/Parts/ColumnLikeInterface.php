<?php

namespace Falgun\Typo\Query\Parts;

use Falgun\Typo\Query\SQLableInterface;

interface ColumnLikeInterface extends SQLableInterface
{

    public function as(string $alias): ColumnLikeInterface;
}
