<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Select;

use PHPUnit\Framework\TestCase;
use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Stubs\Metas\PostsMeta;
use Falgun\Typo\Tests\Integration\AbstractIntegrationTest;

final class BasicSelectQueryTest extends AbstractIntegrationTest
{

    public function testSimpleSelectQuery()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->select(
                $userMeta->id(),
                $userMeta->name()->as('full_name'),
                $userMeta->username(),
            )
            ->from($userMeta->table())
            ->where($userMeta->id()->eq(1))
            ->orderBy($userMeta->id())
            ->limit(0, 100);

        $this->assertSame(
            <<<SQL
            SELECT users.id, users.name as full_name, users.username
            FROM users
            WHERE users.id = ?
            ORDER BY users.id ASC
            LIMIT 0, 100
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [1],
            $query->getBindValues()
        );
    }

    public function testSimpleJoinSelectQuery()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $postMeta = PostsMeta::new();

        $query = $builder
            ->select(
                $userMeta->id(),
                $userMeta->name()->as('full_name'),
                $userMeta->username(),
                $postMeta->title(),
            )
            ->from($userMeta->table())
            ->join($postMeta->table()->on($postMeta->user_id()->eq($userMeta->id())))
            ->orderBy($postMeta->id());

        $this->assertSame(
            <<<SQL
            SELECT users.id, users.name as full_name, users.username, posts.title
            FROM users
            INNER JOIN posts ON posts.user_id = users.id
            ORDER BY posts.id ASC
            
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [],
            $query->getBindValues()
        );
    }
}
