<?php

namespace Falgun\Typo\Interfaces;

interface ConditionInterface extends SQLableInterface
{

    public function and(ConditionInterface $condition): ConditionInterface;

    public function or(ConditionInterface $condition): ConditionInterface;
}
