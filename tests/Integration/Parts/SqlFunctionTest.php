<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Parts;

use Falgun\Typo\Query\Parts\SqlFunction;
use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Stubs\Metas\PostsMeta;
use Falgun\Typo\Tests\Integration\AbstractIntegrationTest;

final class SqlFunctionTest extends AbstractIntegrationTest
{

    public function testSelectCountQuery()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->select(
                SqlFunction::call('COUNT', $userMeta->id()),
                SqlFunction::call('AVG', $userMeta->id())->as('avg'),
            )
            ->from($userMeta->table());

        $this->assertSame(
            <<<SQL
            SELECT COUNT(users.id), AVG(users.id) as avg
            FROM users
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [],
            $query->getBindValues()
        );
    }
}
