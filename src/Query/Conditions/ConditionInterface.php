<?php

namespace Falgun\Typo\Query\Conditions;

use Falgun\Typo\Query\SQLableInterface;

interface ConditionInterface extends SQLableInterface
{

    public function and(ConditionInterface $condition): ConditionInterface;

    public function or(ConditionInterface $condition): ConditionInterface;
}
