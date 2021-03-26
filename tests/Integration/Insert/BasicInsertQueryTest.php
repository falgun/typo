<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Insert;

use PHPUnit\Framework\TestCase;
use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Stubs\Metas\PostsMeta;
use Falgun\Typo\Tests\Integration\AbstractIntegrationTest;

final class BasicInsertQueryTest extends AbstractIntegrationTest
{

    public function testSimpleInsertQuery()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->insertInto($userMeta->table(), $userMeta->name(), $userMeta->username())
            ->values('::name::', '::username::');

        $this->assertSame(
            <<<SQL
            INSERT INTO users (users.name, users.username) VALUES (?, ?)
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [
                '::name::',
                '::username::',
            ],
            $query->getBindValues()
        );
    }

    public function testMultiInsertQuery()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->insertInto($userMeta->table(), $userMeta->name(), $userMeta->username())
            ->values('::name1::', '::username1::')
            ->values('::name2::', '::username2::')
            ->values('::name3::', '::username3::');

        $this->assertSame(
            <<<SQL
            INSERT INTO users (users.name, users.username) VALUES (?, ?), (?, ?), (?, ?)
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [
                '::name1::',
                '::username1::',
                '::name2::',
                '::username2::',
                '::name3::',
                '::username3::',
            ],
            $query->getBindValues()
        );
    }
}
