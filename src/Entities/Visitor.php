<?php

namespace Aselsan\Visitors\Entities;

use CodeIgniter\Entity\Entity;


class Visitor extends Entity
{
    protected $dates = [
        'created_at',
    ];
    protected $casts = [
        'user_id'    => '?int',
        'visitor'    => '?int',
        'user_agent' => 'string',
        'scheme'     => 'string',
        'host'       => 'string',
        'port'       => 'string',
        'user'       => 'string',
        'pass'       => 'string',
        'path'       => 'string',
        'query'      => 'string',
        'fragment'   => 'string',
        'views'      => 'int',
    ];

    /**
     * Converts string IP addresses to their database integer format.
     *
     * @param int|string|null $ipAddress
     */
    public function setIpAddress($ipAddress): void
    {
        if (is_string($ipAddress)) {
            $this->attributes['ip_address'] = ip2long($ipAddress) ?: null;

            return;
        }

        if (is_int($ipAddress) && long2ip($ipAddress)) {
            $this->attributes['ip_address'] = $ipAddress;

            return;
        }

        $this->attributes['ip_address'] = null;
    }

    /**
     * Converts integer IP addresses to their human pointed format.
     */
    public function getIpAddress(): ?string
    {
        if (is_numeric($this->attributes['ip_address'])) {
            return long2ip($this->attributes['ip_address']);
        }

        return null;
    }
}
