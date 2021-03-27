<?php

namespace Falgun\Typo\Interfaces;

interface ColumnLikeInterface extends SQLableInterface
{

    public function as(string $alias): ColumnLikeInterface;
}
