<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Delete;

use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Stubs\Metas\PostsMeta;
use Falgun\Typo\Tests\Integration\AbstractIntegrationTest;

final class JoinDeleteQueryTest extends AbstractIntegrationTest
{

    public function testDefaultJoin()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $postMeta = PostsMeta::new();

        $query = $builder
            ->delete($userMeta->table())
            ->join($postMeta->table()->on($postMeta->userId()->eq($userMeta->id())))
            ->where($userMeta->id()->eq(50));

        $this->assertSame(
            <<<SQL
            DELETE FROM users
            JOIN posts ON posts.user_id = users.id
            WHERE users.id = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [50],
            $query->getBindValues()
        );
    }

    public function testInnerJoin()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $postMeta = PostsMeta::new();

        $query = $builder
            ->delete($userMeta->table())
            ->innerJoin($postMeta->table()->on($postMeta->userId()->eq($userMeta->id())))
            ->where($userMeta->id()->eq(1));

        $this->assertSame(
            <<<SQL
            DELETE FROM users
            INNER JOIN posts ON posts.user_id = users.id
            WHERE users.id = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [1],
            $query->getBindValues()
        );
    }

    public function testLeftJoin()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $postMeta = PostsMeta::new();

        $query = $builder
            ->delete($userMeta->table())
            ->leftJoin($postMeta->table()->on($postMeta->userId()->eq($userMeta->id())))
            ->where($userMeta->id()->eq(1));

        $this->assertSame(
            <<<SQL
            DELETE FROM users
            LEFT JOIN posts ON posts.user_id = users.id
            WHERE users.id = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [1],
            $query->getBindValues()
        );
    }

    public function testSelfJoin()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $userMeta2 = UsersMeta::as('u2');

        $query = $builder
            ->delete($userMeta->table())
            ->join($userMeta2->table()->on($userMeta2->id()->eq($userMeta->id())))
            ->where($userMeta->id()->eq(1));

        $this->assertSame(
            <<<SQL
            DELETE FROM users
            JOIN users as u2 ON u2.id = users.id
            WHERE users.id = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [1],
            $query->getBindValues()
        );
    }

    public function testJoinWithUsing()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $postMeta = PostsMeta::new();

        $query = $builder
            ->delete($userMeta->table())
            ->join($postMeta->table()->using($userMeta->id()))
            ->where($userMeta->id()->eq(1));

        $this->assertSame(
            <<<SQL
            DELETE FROM users
            JOIN posts USING (id)
            WHERE users.id = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [1],
            $query->getBindValues()
        );
    }

    public function testJoinWithMultipleCondition()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $postMeta = PostsMeta::new();

        $query = $builder
            ->delete($userMeta->table())
            ->join(
                $postMeta->table()->on(
                    $postMeta->userId()->eq($userMeta->id())
                    ->and($postMeta->id()->eq(1)
                        ->or($postMeta->id()->between(11, 15)))
                )
            )
            ->where($userMeta->id()->eq(1));

        $this->assertSame(
            <<<SQL
            DELETE FROM users
            JOIN posts ON (posts.user_id = users.id AND (posts.id = ? OR (posts.id BETWEEN ? AND ?)))
            WHERE users.id = ?
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [1, 11, 15, 1],
            $query->getBindValues()
        );
    }

    public function testJoinedColumnInCondition()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $postMeta = PostsMeta::new();

        $query = $builder
            ->delete($userMeta->table())
            ->join($postMeta->table()->on($postMeta->userId()->eq($userMeta->id())))
            ->where($userMeta->name()->eq($postMeta->title()));

        $this->assertSame(
            <<<SQL
            DELETE FROM users
            JOIN posts ON posts.user_id = users.id
            WHERE users.name = posts.title
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [],
            $query->getBindValues()
        );
    }
}
