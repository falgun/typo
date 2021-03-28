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
            ->join($postMeta->table()->on($postMeta->user_id()->eq($userMeta->id())))
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
}
