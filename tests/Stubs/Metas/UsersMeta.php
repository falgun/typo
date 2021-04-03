<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Stubs\Metas;

use Falgun\Typo\Query\Parts\Table;
use Falgun\Typo\Query\Parts\Column;
use Falgun\Typo\Query\Parts\Asterisk;

final class UsersMeta
{

    private const NAME = 'users';

    private string $alias;

    private function __construct(string $alias = '')
    {
        $this->alias = $alias;
    }

    public static function new()
    {
        return new static();
    }

    public static function as(string $alias)
    {
        return new static($alias);
    }

    public function table(): Table
    {
        $table = Table::fromName(self::NAME);

        if ($this->alias !== '') {
            return $table->as($this->alias);
        }

        return $table;
    }

    private function getNameOrAlias(): string
    {
        return ($this->alias ? $this->alias : self::NAME);
    }

    public function asterisk(): Asterisk
    {
        return Asterisk::fromTable($this->getNameOrAlias());
    }

    public function id(): Column
    {
        return Column::fromSchema($this->getNameOrAlias() . '.id');
    }

    public function name(): Column
    {
        return Column::fromSchema($this->getNameOrAlias() . '.name');
    }

    public function username(): Column
    {
        return Column::fromSchema($this->getNameOrAlias() . '.username');
    }
}
