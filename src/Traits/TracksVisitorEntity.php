<?php

namespace Aselsan\Visitors\Traits;

use Myth\Collection\Collection;

trait TracksVisitorsEntity
{
    public function getVisitors(): ?Collection
    {
        if (! array_key_exists('visitors', $this->attributes)) {
            return null;
        }

        return $this->getVisitorsCollection();
    }

    private function getVisitorsCollection(): Collection
    {
        if (! isset($this->attributes['visitors'])) {
            $this->attributes['visitors'] = new Collection([]);
        }

        return $this->attributes['visitors'];
    }
}
