<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Select;

use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Stubs\Metas\PostsMeta;
use Falgun\Typo\Tests\Integration\AbstractIntegrationTest;

final class GroupByQueryTest extends AbstractIntegrationTest
{

    public function testSingleGroup()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->select($userMeta->id())
            ->from($userMeta->table())
            ->groupBy($userMeta->id());

        $this->assertSame(
            <<<SQL
            SELECT users.id
            FROM users
            GROUP BY users.id
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [],
            $query->getBindValues()
        );
    }

    public function testMultipleGroup()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->select($userMeta->id())
            ->from($userMeta->table())
            ->groupBy($userMeta->name(), $userMeta->id())
            ->orderBy($userMeta->id()->asc());

        $this->assertSame(
            <<<SQL
            SELECT users.id
            FROM users
            GROUP BY users.name, users.id
            ORDER BY users.id ASC
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [],
            $query->getBindValues()
        );
    }
}
