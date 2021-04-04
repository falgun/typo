<?php

namespace Falgun\Typo\Query;

interface SQLableInterface
{

    public function getSQL(): string;

    public function getBindValues(): array;
}
