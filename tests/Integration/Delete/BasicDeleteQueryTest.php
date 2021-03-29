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
            ->where($userMeta->username()->eq('Admin'));

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
}
