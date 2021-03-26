<?php

namespace Falgun\Typo\Interfaces;

use Falgun\Typo\Conditions\ConditionInterface;

interface JoinInterface
{

    public function asLeft(): JoinInterface;

    public function on(ConditionInterface $condition): JoinInterface;
}
