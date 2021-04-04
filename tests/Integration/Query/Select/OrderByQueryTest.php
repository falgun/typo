<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Query\Select;

use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Stubs\Metas\PostsMeta;
use Falgun\Typo\Tests\Integration\Query\AbstractIntegrationTest;

final class OrderByQueryTest extends AbstractIntegrationTest
{

    public function testAscOrder()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->select($userMeta->id())
            ->from($userMeta->table())
            ->orderBy($userMeta->id()->asc());

        $this->assertSame(
            <<<SQL
            SELECT users.id
            FROM users
            ORDER BY users.id ASC
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [],
            $query->getBindValues()
        );
    }

    public function testDescOrder()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->select($userMeta->id())
            ->from($userMeta->table())
            ->orderBy($userMeta->id()->desc());

        $this->assertSame(
            <<<SQL
            SELECT users.id
            FROM users
            ORDER BY users.id DESC
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [],
            $query->getBindValues()
        );
    }

    public function testMultipleOrder()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->select($userMeta->id())
            ->from($userMeta->table())
            ->orderBy(
            $userMeta->name()->asc(),
            $userMeta->username()->desc(),
            $userMeta->id()->asc()
        );

        $this->assertSame(
            <<<SQL
            SELECT users.id
            FROM users
            ORDER BY users.name ASC, users.username DESC, users.id ASC
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [],
            $query->getBindValues()
        );
    }
}
