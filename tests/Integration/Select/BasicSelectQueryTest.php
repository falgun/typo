<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Select;

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
            ->orderBy($userMeta->id()->asc())
            ->limit(0, 100);

        $this->assertSame(
            <<<SQL
            SELECT users.id, users.name as full_name, users.username
            FROM users
            WHERE users.id = ?
            ORDER BY users.id ASC
            LIMIT ?, ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [1, 0, 100],
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
            ->join($postMeta->table()->on($postMeta->userId()->eq($userMeta->id())))
            ->orderBy($postMeta->id()->desc());

        $this->assertSame(
            <<<SQL
            SELECT users.id, users.name as full_name, users.username, posts.title
            FROM users
            JOIN posts ON posts.user_id = users.id
            ORDER BY posts.id DESC
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [],
            $query->getBindValues()
        );
    }
}
