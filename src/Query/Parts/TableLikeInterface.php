<?php

namespace Falgun\Typo\Query\Parts;

use Falgun\Typo\Query\SQLableInterface;

interface TableLikeInterface extends SQLableInterface
{

    public function as(string $alias): TableLikeInterface;
}
