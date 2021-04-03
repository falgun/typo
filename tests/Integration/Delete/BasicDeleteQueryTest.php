<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Delete;

use PHPUnit\Framework\TestCase;
use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Stubs\Metas\PostsMeta;
use Falgun\Typo\Tests\Integration\AbstractIntegrationTest;

final class BasicDeleteQueryTest extends AbstractIntegrationTest
{

    public function testSimpleDeleteQuery()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->delete($userMeta->table())
            ->where($userMeta->id()->eq(3));

        $this->assertSame(
            <<<SQL
            DELETE FROM users
            WHERE users.id = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [3],
            $query->getBindValues()
        );
    }

    public function testMultiConditionDeleteQuery()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->delete($userMeta->table())
            ->where($userMeta->id()->eq(3))
            ->orWhere($userMeta->id()->eq(5))
            ->andWhere($userMeta->username()->eq('Admin'));

        $this->assertSame(
            <<<SQL
            DELETE FROM users
            WHERE users.id = ? OR users.id = ? AND users.username = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [3, 5, 'Admin'],
            $query->getBindValues()
        );
    }

    public function testDeleteQueryWithOrderBy()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->delete($userMeta->table())
            ->where($userMeta->id()->eq(3))
            ->orderBy($userMeta->id()->desc());

        $this->assertSame(
            <<<SQL
            DELETE FROM users
            WHERE users.id = ?
            ORDER BY users.id DESC
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [3],
            $query->getBindValues()
        );
    }

    public function testDeleteQueryWithOffsetLimit()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->delete($userMeta->table())
            ->where($userMeta->id()->eq(3))
            ->orderBy($userMeta->id()->desc())
            ->limit(0, 100);

        $this->assertSame(
            <<<SQL
            DELETE FROM users
            WHERE users.id = ?
            ORDER BY users.id DESC
            LIMIT ?, ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [3, 0, 100],
            $query->getBindValues()
        );
    }

    public function testDeleteQueryWithOnlyLimit()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->delete($userMeta->table())
            ->where($userMeta->id()->eq(3))
            ->orderBy($userMeta->id()->desc())
            ->limit(50);

        $this->assertSame(
            <<<SQL
            DELETE FROM users
            WHERE users.id = ?
            ORDER BY users.id DESC
            LIMIT ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [3, 50],
            $query->getBindValues()
        );
    }
}
