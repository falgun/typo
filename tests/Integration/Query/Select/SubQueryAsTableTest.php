<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Query\Select;

use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Stubs\Metas\PostsMeta;
use Falgun\Typo\Tests\Integration\Query\AbstractIntegrationTest;

final class SubQueryAsTableTest extends AbstractIntegrationTest
{

    public function testSubqueryOnSelectFrom()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $userMeta2 = UsersMeta::as('u2');

        $query = $builder
            ->select(
                $userMeta2->id(),
            )
            ->from(
            $builder->select($userMeta->id(), $userMeta->name())
            ->from($userMeta->table())
            ->where($userMeta->id()->eq(100))
            ->asTable('u2')
        );

        $this->assertSame(
            <<<SQL
            SELECT u2.id
            FROM (SELECT users.id, users.name
            FROM users
            WHERE users.id = ?) as u2
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [100],
            $query->getBindValues()
        );
    }
}
