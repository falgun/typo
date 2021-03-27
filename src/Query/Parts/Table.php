<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Parts;

use Falgun\Typo\Interfaces\JoinInterface;
use Falgun\Typo\Conditions\ConditionInterface;
use Falgun\Typo\Interfaces\TableLikeInterface;

final class Table implements TableLikeInterface
{

    private string $name;
    private string $alias;

    private function __construct(string $name, string $alias = '')
    {
        $this->name = $name;
        $this->alias = $alias;
    }

    public static function fromName(string $name): static
    {
        return new static($name);
    }

    public function as(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function on(ConditionInterface $condition): JoinInterface
    {
        return Join::new($this)->on($condition);
    }

    public function getSQL(): string
    {
        return ($this->alias ? $this->name . ' as ' . $this->alias : $this->name);
    }
}
