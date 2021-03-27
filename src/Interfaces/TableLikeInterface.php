<?php

namespace Falgun\Typo\Interfaces;

interface TableLikeInterface extends SQLableInterface
{

    public function as(string $alias): TableLikeInterface;
}
