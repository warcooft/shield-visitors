<?php

namespace Aselsan\Visitors\Traits;

use Aselsan\Visitors\Models\VisitorModel;

trait HasVisitors
{
    protected bool $includeVisitors = false;

    /**
     * Set up model events and initialize visitors related stuff.
     */
    protected function initVisitors(): void
    {
        $this->afterFind[]     = 'trackVisitorsAfterFind';
        $this->allowedFields[] = 'visitors';

        helper('inflector');
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

        $model = model(VisitorModel::class);

        if ($eventData['singleton']) {
            if ($this->tempReturnType === 'array') {
                $eventData['data']['visitors'] = $this->find($model->getById($eventData['data'][$this->primaryKey]));
            } else {
                $eventData['data']->visitors = $this->find($model->getById($eventData['data']->{$this->primaryKey}));
            }
        } else {
            $user_ids = array_map('intval', array_column($eventData['data'], $this->primaryKey));
            $visitors = $model->getByIds($user_ids);

            foreach ($eventData['data'] as &$data) {
                if ($this->tempReturnType === 'array') {
                    $data['visitors'] = $visitors[$data[$this->primaryKey]] ?? [];
                } else {
                    $data->visitors = $visitors[$data->{$this->primaryKey}] ?? [];
                }
            }
        }

        $this->includeVisitors = false;

        return $eventData;
    }
}
