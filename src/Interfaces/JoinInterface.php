<?php

namespace Falgun\Typo\Interfaces;

use Falgun\Typo\Interfaces\SQLableInterface;
use Falgun\Typo\Conditions\ConditionInterface;

interface JoinInterface extends SQLableInterface
{

    public function asInner(): JoinInterface;

    public function asLeft(): JoinInterface;

    public function on(ConditionInterface $condition): JoinInterface;

    public function using(ColumnLikeInterface $column): JoinInterface;
}
