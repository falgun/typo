<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Update;

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
            ->update($userMeta->table())
            ->join($postMeta->table()->on($postMeta->user_id()->eq($userMeta->id())))
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
}
