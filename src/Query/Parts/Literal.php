<?php
declare(strict_types=1);

namespace Falgun\Typo\Query\Parts;

use Falgun\Typo\Query\SQLableInterface;

final class Literal implements SQLableInterface
{

    /**
     *
     * @var mixed
     */
    private $value;

    /**
     *
     * @param mixed $value
     */
    private function __construct($value)
    {
        $this->value = $value;
    }

    /**
     *
     * @param mixed $value
     *
     * @return static
     */
    public static function from($value): static
    {
        return new static($value);
    }

    public function getSQL(): string
    {
        return (string) $this->value;
    }

    public function getBindValues(): array
    {
        return [];
    }
}
