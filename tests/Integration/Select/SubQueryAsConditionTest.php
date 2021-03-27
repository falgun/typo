<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Select;

use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Stubs\Metas\PostsMeta;
use Falgun\Typo\Tests\Integration\AbstractIntegrationTest;

final class SubQueryAsConditionTest extends AbstractIntegrationTest
{

    public function testSubqueryOnCodition()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $postMeta = PostsMeta::new();

        $query = $builder
            ->select($userMeta->id())
            ->from($userMeta->table())
            ->where($userMeta->id()->eq(
                $builder->select($postMeta->id())
                ->from($postMeta->table())
                ->where($postMeta->user_id()->eq(101))
                ->as(''))
        );

        $this->assertSame(
            <<<SQL
            SELECT users.id
            FROM users
            WHERE users.id = (SELECT posts.id
            FROM posts
            WHERE posts.user_id = ?)
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [101],
            $query->getBindValues()
        );
    }
}
