<?php

namespace Falgun\Typo\Interfaces;

interface SQLableInterface
{

    public function getSQL(): string;

    public function getBindValues(): array;
}
