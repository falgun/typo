<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Stubs\Metas;

use Falgun\Typo\Query\Parts\Table;
use Falgun\Typo\Query\Parts\Column;

final class PostsMeta
{

    private const NAME = 'posts';

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

    public function category_id(): Column
    {
        return Column::fromSchema($this->alias . '.category_id');
    }

    public function user_id(): Column
    {
        return Column::fromSchema($this->alias . '.user_id');
    }

    public function title(): Column
    {
        return Column::fromSchema($this->alias . '.title');
    }
}