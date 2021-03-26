<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Stubs\Metas;

use Falgun\Typo\Query\Parts\Table;
use Falgun\Typo\Query\Parts\Column;

final class UsersMeta
{

    private const NAME = 'users';

    private string $alias;

    private function __construct(string $alias)
    {
        $this->alias = $alias;
    }

    public static function new()
    {
        return new static(static::NAME);
    }

    public static function as(string $alias)
    {
        return new static($alias);
    }

    public function table(): Table
    {
        return Table::fromName(self::NAME);
    }

    public function id(): Column
    {
        return Column::fromSchema($this->alias . '.id');
    }

    public function name(): Column
    {
        return Column::fromSchema($this->alias . '.name');
    }

    public function username(): Column
    {
        return Column::fromSchema($this->alias . '.username');
    }
}
