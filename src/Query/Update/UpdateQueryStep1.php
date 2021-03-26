<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Update;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Parts\Table;
use Falgun\Typo\Query\Parts\Column;
use Falgun\Typo\Conditions\ConditionInterface;

final class UpdateQueryStep1
{

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Kuery $kuery;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Table $table;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private array $updatableColumns;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private array $updatableValues;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private function __construct()
    {
        
    }

    public static function fromTable(Kuery $kuery, Table $table): static
    {
        $object = new static;
        $object->kuery = $kuery;
        $object->table = $table;

        return $object;
    }

    /**
     *
     * @param Column $column
     * @param mixed $value
     *
     * @return self
     */
    public function set(Column $column, $value): self
    {
        $this->updatableColumns[] = $column;
        $this->updatableValues[] = $value;

        return $this;
    }

    public function where(ConditionInterface $condition): UpdateQueryStep2
    {
        return UpdateQueryStep2::fromCondition(
                $this->kuery,
                $this->table,
                $this->updatableColumns,
                $this->updatableValues,
                $condition,
        );
    }
}
