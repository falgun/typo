<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Query\Conditions;

use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Stubs\Metas\PostsMeta;
use Falgun\Typo\Tests\Integration\Query\AbstractIntegrationTest;

final class InTest extends AbstractIntegrationTest
{

    public function testInCondition()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();
        $postMeta = PostsMeta::new();

        $query = $builder
            ->select($userMeta->id())
            ->from($userMeta->table())
            ->where($userMeta->id()->in(75))
            ->orWhere($userMeta->id()->in([25, 50, 100]))
            ->orWhere($userMeta->id()->in(
                $builder->select($postMeta->id())
                ->from($postMeta->table())
                ->as('')
        ));

        $this->assertSame(
            <<<SQL
            SELECT users.id
            FROM users
            WHERE users.id IN (?) OR users.id IN (?, ?, ?) OR users.id IN ((SELECT posts.id
            FROM posts))
            SQL,
            $query->getSQL()
        );

        $this->assertSame(
            [75, 25, 50, 100],
            $query->getBindValues()
        );
    }
}
