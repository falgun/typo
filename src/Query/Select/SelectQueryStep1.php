<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Select;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Conditions\ConditionInterface;
use Falgun\Typo\Interfaces\TableLikeInterface;
use Falgun\Typo\Interfaces\ColumnLikeInterface;

final class SelectQueryStep1
{

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Kuery $kuery;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private array $selectedColumns;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private function __construct()
    {
        
    }

    public static function selectColumns(Kuery $kuery, array $columns): static
    {
        $object = new static;
        $object->kuery = $kuery;
        $object->selectedColumns = $columns;

        return $object;
    }

    public function from(TableLikeInterface $table): SelectQueryStep2
    {
        return SelectQueryStep2::fromTable($this->kuery, $this->selectedColumns, $table);
    }
}
