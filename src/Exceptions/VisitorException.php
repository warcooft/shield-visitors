<?php

declare(strict_types=1);

namespace Aselsan\Visitors\Exceptions;

use RuntimeException;

class VisitorException extends RuntimeException
{
    /**
     * Thrown when the supplied minutes falls outside the
     * range of allowed minutes.
     *
     * @return static
     */
    public static function forInvalidMinutes(string $minutes)
    {
        return new static(lang('Time.invalidMinutes', [$minutes]));
    }
}
