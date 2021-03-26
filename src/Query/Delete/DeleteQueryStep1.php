<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Delete;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Parts\Table;
use Falgun\Typo\Query\Parts\Column;
use Falgun\Typo\Conditions\ConditionInterface;

final class DeleteQueryStep1
{

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Kuery $kuery;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Table $table;

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

    public function where(ConditionInterface $condition): DeleteQueryStep2
    {
        return DeleteQueryStep2::fromCondition(
                $this->kuery,
                $this->table,
                $condition,
        );
    }
}
