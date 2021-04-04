<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Select;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Conditions\ConditionInterface;
use Falgun\Typo\Query\Parts\TableLikeInterface;
use Falgun\Typo\Query\Parts\ColumnLikeInterface;

final class SelectQueryStep1
{

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Kuery $kuery;

    /**
     * @var array<int, ColumnLikeInterface>
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private array $selectedColumns;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private function __construct()
    {
        
    }

    /**
     * @param Kuery $kuery
     * @param array<int, ColumnLikeInterface> $columns
     *
     * @return static
     */
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
