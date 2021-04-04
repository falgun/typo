<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Insert;

use Falgun\Kuery\Kuery;
use Falgun\Typo\Query\Parts\Table;
use Falgun\Typo\Query\Parts\Column;

final class InsertQueryStep1
{

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Kuery $kuery;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private Table $table;

    /**
     * @var array<int, Column>
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private array $columns;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private function __construct()
    {
        
    }

    public static function into(Kuery $kuery, Table $table, Column ...$columns): static
    {
        $object = new static;
        $object->kuery = $kuery;
        $object->table = $table;
        $object->columns = $columns;

        return $object;
    }

    /**
     *
     * @param mixed $values
     *
     * @return InsertQueryStep2
     * @throws \InvalidArgumentException
     */
    public function values(...$values): InsertQueryStep2
    {
        if (count($this->columns) !== count($values)) {
            throw new \InvalidArgumentException('Count of values did not match with count of columns');
        }

        return InsertQueryStep2::fromStep1(
                $this->kuery,
                $this->table,
                $this->columns,
                $values
        );
    }
}
