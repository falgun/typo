<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Update;

use PHPUnit\Framework\TestCase;
use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Stubs\Metas\PostsMeta;
use Falgun\Typo\Tests\Integration\AbstractIntegrationTest;

final class BasicUpdateQueryTest extends AbstractIntegrationTest
{

    public function testSimpleUpdateQuery()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->update($userMeta->table())
            ->set($userMeta->name(), '::new name::')
            ->set($userMeta->username(), '::new username::')
            ->where($userMeta->id()->eq(2));

        $this->assertSame(
            <<<SQL
            UPDATE users
            SET users.name = ?, users.username = ?
            WHERE users.id = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [
                '::new name::',
                '::new username::',
                2,
            ],
            $query->getBindValues()
        );
    }

    public function testMultiConditionUpdateQuery()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->update($userMeta->table())
            ->set($userMeta->name(), '::new name::')
            ->where($userMeta->id()->eq(2))
            ->andWhere($userMeta->id()->eq(3))
            ->orWhere($userMeta->username()->eq('Admin'));

        $this->assertSame(
            <<<SQL
            UPDATE users
            SET users.name = ?
            WHERE users.id = ? AND users.id = ? OR users.username = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [
                '::new name::',
                2,
                3,
                'Admin',
            ],
            $query->getBindValues()
        );
    }
}
