<?php

namespace Falgun\Typo\Interfaces;

interface ConditionInterface extends SQLableInterface
{

    public function asAnd(): ConditionInterface;

    public function asOr(): ConditionInterface;

    public function and(ConditionInterface $condition): ConditionInterface;

    public function or(ConditionInterface $condition): ConditionInterface;
}
