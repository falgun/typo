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
                ->as('post_title')
                )
            )
            ->from($userMeta->table());

        $this->assertSame(
            <<<SQL
            SELECT users.id, (SELECT posts.title
            FROM posts
            WHERE posts.user_id = users.id) as post_title
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
