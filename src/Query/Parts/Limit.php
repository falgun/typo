<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Parts;

use Falgun\Typo\Query\SQLableInterface;

final class Limit implements SQLableInterface
{

    private ?int $offset;
    private ?int $limit;

    private function __construct(?int $offset, ?int $limit)
    {
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public static function fromOffsetLimit(int $offsetOrLimit, ?int $limit): Limit
    {
        if ($limit === null) {
            $offset = null;
            $limit = $offsetOrLimit;
        } else {
            $offset = $offsetOrLimit;
            $limit = $limit;
        }

        return new static($offset, $limit);
    }

    public static function fromBlank(): Limit
    {
        return new static(null, null);
    }

    public function getBindValues(): array
    {
        if (isset($this->offset) && isset($this->limit)) {
            // both offset & limit has been provided
            return [$this->offset, $this->limit];
        } elseif (isset($this->limit)) {
            // only limit
            return [$this->limit];
        }

        return [];
    }

    public function getSQL(): string
    {
        if (isset($this->offset) && isset($this->limit)) {
            // both offset & limit has been provided
            return PHP_EOL . 'LIMIT ?, ?';
        } elseif (isset($this->limit)) {
            // only limit
            return PHP_EOL . 'LIMIT ?';
        }

        return '';
    }
}
