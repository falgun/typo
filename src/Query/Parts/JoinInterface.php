<?php

namespace Falgun\Typo\Query\Parts;

use Falgun\Typo\Query\SQLableInterface;
use Falgun\Typo\Query\Conditions\ConditionInterface;

interface JoinInterface extends SQLableInterface
{

    public function asInner(): JoinInterface;

    public function asLeft(): JoinInterface;

    public function on(ConditionInterface $condition): JoinInterface;

    public function using(ColumnLikeInterface $column): JoinInterface;
}
