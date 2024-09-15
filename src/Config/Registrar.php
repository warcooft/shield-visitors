<?php

namespace Aselsan\Visitors\Config;

use Aselsan\Visitors\Filters\VisitorsFilter;

class Registrar
{
    /**
     * Registers the Shield filters.
     */
    public static function Filters(): array
    {
        return [
            'aliases' => [
                'visitors' => VisitorsFilter::class
            ],
        ];
    }
}
