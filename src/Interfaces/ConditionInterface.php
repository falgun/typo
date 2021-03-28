<?php

namespace Falgun\Typo\Interfaces;

interface ConditionInterface extends SQLableInterface
{

    public function asAnd(): ConditionInterface;

    public function asOr(): ConditionInterface;
}
