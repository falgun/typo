<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Select;

use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Stubs\Metas\PostsMeta;
use Falgun\Typo\Tests\Integration\AbstractIntegrationTest;

final class SubQueryAsColumnTest extends AbstractIntegrationTest
{

    public function testSubqueryOnSelectColumn()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $postMeta = PostsMeta::new();

        $query = $builder
            ->select(
                $userMeta->id(),
                (
                $builder->select($postMeta->title())
                ->from($postMeta->table())
                ->where($postMeta->user_id()->eq($userMeta->id()))
                ->andWhere($postMeta->id()->eq(99))
                ->as('post_title')
                )
            )
            ->from($userMeta->table());

        $this->assertSame(
            <<<SQL
            SELECT users.id, (SELECT posts.title
            FROM posts
            WHERE posts.user_id = users.id AND posts.id = ?) as post_title
            FROM users
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [99],
            $query->getBindValues()
        );
    }

    public function testSubqueryOnSelectSelfColumn()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $userMeta2 = UsersMeta::as('u2');

        $query = $builder
            ->select(
                $userMeta->id(),
                (
                $builder->select($userMeta2->id())
                ->from($userMeta2->table())
                ->where($userMeta2->id()->eq($userMeta->id()))
                ->as('mirrored_id')
                )
            )
            ->from($userMeta->table());

        $this->assertSame(
            <<<SQL
            SELECT users.id, (SELECT u2.id
            FROM users as u2
            WHERE u2.id = users.id) as mirrored_id
            FROM users
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [],
            $query->getBindValues()
        );
    }
}
