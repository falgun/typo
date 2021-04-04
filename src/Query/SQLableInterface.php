<?php

namespace Falgun\Typo\Query;

interface SQLableInterface
{

    public function getSQL(): string;

    /**
     * @return array<int, mixed>
     */
    public function getBindValues(): array;
}
