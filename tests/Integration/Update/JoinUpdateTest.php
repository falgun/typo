<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Update;

use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Stubs\Metas\PostsMeta;
use Falgun\Typo\Tests\Integration\AbstractIntegrationTest;

final class JoinUpdateTest extends AbstractIntegrationTest
{

    public function testDefaultJoin()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $postMeta = PostsMeta::new();

        $query = $builder
            ->update($userMeta->table())
            ->join($postMeta->table()->on($postMeta->userId()->eq($userMeta->id())))
            ->set($userMeta->name(), '::newName::')
            ->set($userMeta->username(), $postMeta->title())
            ->where($userMeta->id()->eq(50));

        $this->assertSame(
            <<<SQL
            UPDATE users
            JOIN posts ON posts.user_id = users.id
            SET users.name = ?, users.username = posts.title
            WHERE users.id = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            ['::newName::', 50],
            $query->getBindValues()
        );
    }

    public function testInnerJoin()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $postMeta = PostsMeta::new();

        $query = $builder
            ->update($userMeta->table())
            ->innerJoin($postMeta->table()->on($postMeta->userId()->eq($userMeta->id())))
            ->set($userMeta->name(), '::name::')
            ->where($userMeta->id()->eq(1));

        $this->assertSame(
            <<<SQL
            UPDATE users
            INNER JOIN posts ON posts.user_id = users.id
            SET users.name = ?
            WHERE users.id = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            ['::name::', 1],
            $query->getBindValues()
        );
    }

    public function testLeftJoin()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $postMeta = PostsMeta::new();

        $query = $builder
            ->update($userMeta->table())
            ->leftJoin($postMeta->table()->on($postMeta->userId()->eq($userMeta->id())))
            ->set($userMeta->name(), '::name::')
            ->where($userMeta->id()->eq(1));

        $this->assertSame(
            <<<SQL
            UPDATE users
            LEFT JOIN posts ON posts.user_id = users.id
            SET users.name = ?
            WHERE users.id = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            ['::name::', 1],
            $query->getBindValues()
        );
    }

    public function testSelfJoin()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $userMeta2 = UsersMeta::as('u2');

        $query = $builder
            ->update($userMeta->table())
            ->join($userMeta2->table()->on($userMeta2->id()->eq($userMeta->id())))
            ->set($userMeta->name(), '::name::')
            ->where($userMeta->id()->eq(1));

        $this->assertSame(
            <<<SQL
            UPDATE users
            JOIN users as u2 ON u2.id = users.id
            SET users.name = ?
            WHERE users.id = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            ['::name::', 1],
            $query->getBindValues()
        );
    }

    public function testJoinWithUsing()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $postMeta = PostsMeta::new();

        $query = $builder
            ->update($userMeta->table())
            ->join($postMeta->table()->using($userMeta->id()))
            ->set($userMeta->name(), '::name::')
            ->where($userMeta->id()->eq(1));

        $this->assertSame(
            <<<SQL
            UPDATE users
            JOIN posts USING (id)
            SET users.name = ?
            WHERE users.id = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            ['::name::', 1],
            $query->getBindValues()
        );
    }

    public function testJoinWithMultipleCondition()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $postMeta = PostsMeta::new();

        $query = $builder
            ->update($userMeta->table())
            ->join(
                $postMeta->table()->on(
                    $postMeta->userId()->eq($userMeta->id())
                    ->and($postMeta->id()->eq(1)
                        ->or($postMeta->id()->between(11, 15)))
                )
            )
            ->set($userMeta->name(), '::name::')
            ->where($userMeta->id()->eq(1));

        $this->assertSame(
            <<<SQL
            UPDATE users
            JOIN posts ON (posts.user_id = users.id AND (posts.id = ? OR (posts.id BETWEEN ? AND ?)))
            SET users.name = ?
            WHERE users.id = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [1, 11, 15, '::name::', 1],
            $query->getBindValues()
        );
    }

    public function testJoinedColumnInCondition()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $postMeta = PostsMeta::new();

        $query = $builder
            ->update($userMeta->table())
            ->join($postMeta->table()->on($postMeta->userId()->eq($userMeta->id())))
            ->set($userMeta->name(), '::name::')
            ->where($userMeta->name()->eq($postMeta->title()));

        $this->assertSame(
            <<<SQL
            UPDATE users
            JOIN posts ON posts.user_id = users.id
            SET users.name = ?
            WHERE users.name = posts.title
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            ['::name::'],
            $query->getBindValues()
        );
    }
}
