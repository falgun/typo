<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Conditions;

use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Stubs\Metas\PostsMeta;
use Falgun\Typo\Tests\Integration\AbstractIntegrationTest;

final class BetweenTest extends AbstractIntegrationTest
{

    public function testBetweenCondition()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->select($userMeta->id())
            ->from($userMeta->table())
            ->where($userMeta->id()->between(1, 99))
            ->andWhere($userMeta->id()->between(150, 200))
            ->orWhere($userMeta->id()->eq(500));

        $this->assertSame(
            <<<SQL
            SELECT users.id
            FROM users
            WHERE (users.id BETWEEN ? AND ?) AND (users.id BETWEEN ? AND ?) OR users.id = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [1, 99, 150, 200, 500],
            $query->getBindValues()
        );
    }

    public function testNestedBetweenCondition()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->select($userMeta->id())
            ->from($userMeta->table())
            ->where(
                $userMeta->id()->between(1, 99)
                ->or($userMeta->name()->between(1, 5)
                    ->and($userMeta->username()->between(9, 15)))
            )
            ->andWhere($userMeta->id()->between(150, 200))
            ->orWhere($userMeta->id()->eq(500));

        $this->assertSame(
            <<<SQL
            SELECT users.id
            FROM users
            WHERE ((users.id BETWEEN ? AND ?) OR ((users.name BETWEEN ? AND ?) AND (users.username BETWEEN ? AND ?))) AND (users.id BETWEEN ? AND ?) OR users.id = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [1, 99, 1, 5, 9, 15, 150, 200, 500],
            $query->getBindValues()
        );
    }

    public function testBetweenAcceptsColumn()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $postMeta = PostsMeta::new();
        $query = $builder
            ->select($userMeta->id())
            ->from($userMeta->table())
            ->leftJoin($postMeta->table()->on($postMeta->userId()->eq($userMeta->id())))
            ->where($userMeta->id()->between($postMeta->id(), 99))
            ->andWhere($userMeta->id()->between(150, $postMeta->id()))
            ->andWhere($userMeta->id()->between($userMeta->id(), $postMeta->id()));

        $this->assertSame(
            <<<SQL
            SELECT users.id
            FROM users
            LEFT JOIN posts ON posts.user_id = users.id
            WHERE (users.id BETWEEN posts.id AND ?) AND (users.id BETWEEN ? AND posts.id) AND (users.id BETWEEN users.id AND posts.id)
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [99, 150],
            $query->getBindValues()
        );
    }

    public function testBetweenAcceptsSubquery()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $postMeta = PostsMeta::new();
        $query = $builder
            ->select($userMeta->id())
            ->from($userMeta->table())
            ->where(
            $userMeta->id()->between(
                $builder->select($postMeta->id())
                ->from($postMeta->table())
                ->orderBy($postMeta->id()->asc())
                ->limit(1)
                ->as(''),
                $builder->select($postMeta->id())
                ->from($postMeta->table())
                ->orderBy($postMeta->id()->desc())
                ->limit(1)
                ->as('')
            )
        );

        $this->assertSame(
            <<<SQL
            SELECT users.id
            FROM users
            WHERE (users.id BETWEEN (SELECT posts.id
            FROM posts
            ORDER BY posts.id ASC
            LIMIT 1) AND (SELECT posts.id
            FROM posts
            ORDER BY posts.id DESC
            LIMIT 1))
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [],
            $query->getBindValues()
        );
    }
}
