<?php

namespace Aselsan\Visitors\Traits;

use Aselsan\Visitors\Models\VisitorModel;
use Myth\Collection\Collection;

trait HasVisitors
{
    protected bool $includeVisitors = false;

    protected Collection $scopeVisitors;
    protected Collection $visitors;
    protected string $visitorType;

    /**
     * Set up model events and initialize visitors related stuff.
     */
    protected function initVisitors(): void
    {
        $this->afterFind[]     = 'trackVisitorsAfterFind';
        $this->allowedFields[] = 'visitors';

        helper('inflector');

        $this->scopeVisitors = new Collection([]);
        $this->visitors      = new Collection([]);
        $this->visitorType   = plural($this->table);
    }

    /**
     * Include visitors to the result.
     */
    public function withVisitors(): static
    {
        $this->includeVisitors = true;

        return $this;
    }

    /**
     * After find event.
     */
    protected function trackVisitorsAfterFind(array $eventData): array
    {
        if (! $this->includeVisitors || empty($eventData['data'])) {
            return $eventData;
        }

        $visitorModel = model(VisitorModel::class);

        if ($eventData['singleton']) {
            if ($this->tempReturnType === 'array') {
                $eventData['data']['visitors'] = new Collection($visitorModel->getById($eventData['data'][$this->primaryKey], $this->visitorType));
            } else {
                $eventData['data']->visitors = new Collection($visitorModel->getById($eventData['data']->{$this->primaryKey}, $this->visitorType));
            }
        } else {
            $keys = array_map('intval', array_column($eventData['data'], $this->primaryKey));
            $visitors = $visitorModel->getByIds($keys, $this->visitorType);

            foreach ($eventData['data'] as &$data) {
                if ($this->tempReturnType === 'array') {
                    $data['visitors'] = new Collection($visitors[$data[$this->primaryKey]] ?? []);
                } else {
                    $data->visitors = new Collection($visitors[$data->{$this->primaryKey}] ?? []);
                }
            }
        }

        $this->includeVisitors = false;

        return $eventData;
    }
}
