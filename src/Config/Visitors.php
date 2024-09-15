<?php

namespace Aselsan\Visitors\Config;

use CodeIgniter\Config\BaseConfig;

class Visitors extends BaseConfig
{
    /**
     * Number of seconds before a visit counts as new
     * instead of incrementing a previous view count.
     * Set to zero to record each page view as unique (not recommended).
     */
    public int $resetAfter = DAY;
}
