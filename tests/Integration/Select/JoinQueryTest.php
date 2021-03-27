<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Select;

use PHPUnit\Framework\TestCase;
use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Stubs\Metas\PostsMeta;
use Falgun\Typo\Tests\Integration\AbstractIntegrationTest;

final class JoinQueryTest extends AbstractIntegrationTest
{

    public function testDefaultJoin()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $postMeta = PostsMeta::new();

        $query = $builder
            ->select(
                $userMeta->id(),
                $postMeta->title(),
            )
            ->from($userMeta->table())
            ->join($postMeta->table()->on($postMeta->user_id()->eq($userMeta->id())));

        $this->assertSame(
            <<<SQL
            SELECT users.id, posts.title
            FROM users
            JOIN posts ON posts.user_id = users.id
            
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [],
            $query->getBindValues()
        );
    }

    public function testInnerJoin()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $postMeta = PostsMeta::new();

        $query = $builder
            ->select(
                $userMeta->id(),
                $postMeta->title(),
            )
            ->from($userMeta->table())
            ->innerJoin($postMeta->table()->on($postMeta->user_id()->eq($userMeta->id())));

        $this->assertSame(
            <<<SQL
            SELECT users.id, posts.title
            FROM users
            INNER JOIN posts ON posts.user_id = users.id
            
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [],
            $query->getBindValues()
        );
    }

    public function testLeftJoin()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $postMeta = PostsMeta::new();

        $query = $builder
            ->select(
                $userMeta->id(),
                $postMeta->title(),
            )
            ->from($userMeta->table())
            ->leftJoin($postMeta->table()->on($postMeta->user_id()->eq($userMeta->id())));

        $this->assertSame(
            <<<SQL
            SELECT users.id, posts.title
            FROM users
            LEFT JOIN posts ON posts.user_id = users.id
            
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [],
            $query->getBindValues()
        );
    }

    public function testSelfJoin()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $userMeta2 = UsersMeta::as('u2');

        $query = $builder
            ->select(
                $userMeta->id(),
                $userMeta2->name(),
            )
            ->from($userMeta->table())
            ->join($userMeta2->table()->on($userMeta2->id()->eq($userMeta->id())));

        $this->assertSame(
            <<<SQL
            SELECT users.id, u2.name
            FROM users
            JOIN users as u2 ON u2.id = users.id
            
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [],
            $query->getBindValues()
        );
    }
}
