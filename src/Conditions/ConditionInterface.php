<?php

namespace Falgun\Typo\Conditions;

use Falgun\Typo\Interfaces\SQLableInterface;

interface ConditionInterface extends SQLableInterface
{

    public function asAnd(): ConditionInterface;

    public function asOr(): ConditionInterface;
}
