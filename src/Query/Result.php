<?php
declare(strict_types=1);

namespace Falgun\Typo\Query;

use mysqli_stmt;
use Falgun\Kuery\Kuery;

final class Result
{

    private mysqli_stmt $stmt;
    private Kuery $kuery;

    private function __construct(mysqli_stmt $stmt, Kuery $kuery)
    {
        $this->stmt = $stmt;
        $this->kuery = $kuery;
    }

    public static function fromStmtKuery(mysqli_stmt $stmt, Kuery $kuery): Result
    {
        return new static($stmt, $kuery);
    }

    public function allAsArray(): array
    {
        return $this->kuery->fetchAllAsArray($this->stmt);
    }

    public function oneAsArray(): ?array
    {
        return $this->kuery->fetchOneAsArray($this->stmt);
    }
}
