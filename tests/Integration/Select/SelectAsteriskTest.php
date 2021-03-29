<?php
declare(strict_types=1);

namespace Falgun\Typo\Tests\Integration\Select;

use Falgun\Typo\Tests\Stubs\Metas\UsersMeta;
use Falgun\Typo\Tests\Integration\AbstractIntegrationTest;

final class SelectAsteriskTest extends AbstractIntegrationTest
{

    public function testSelectAsteriskQuery()
    {
        $builder = $this->getBuilder();

        $userMeta = UsersMeta::new();

        $query = $builder
            ->select($userMeta->asterisk())
            ->from($userMeta->table());

        $this->assertSame(
            <<<SQL
            SELECT users.*
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
