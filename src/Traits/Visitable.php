<?php

namespace Aselsan\Visitors\Traits;

use Aselsan\Visitors\Config\Visitors;
use Aselsan\Visitors\Models\VisitorModel;

trait Visitable
{
    private Visitors $config;
    private $model;

    public function __construct()
    {
        $this->config = config(Visitors::class);
        $this->model  = model(VisitorModel::class);
    }

    public function getVisitors(): ?array
    {
        if (! array_key_exists('visitors', $this->attributes)) {
            return null;
        }

        return $this->attributes['visitors'];
    }

    public function getSumVisitor(): ?int
    {
        if (! array_key_exists('visitors', $this->attributes)) {
            return null;
        }

        return count($this->attributes['visitors']);
    }

    public function visit(): void
    {
        $model  = model(VisitorModel::class);
        // Check for an existing similar record
        if ($similar = $model->findSimilar($this->id)) {
            // Increment view count and update
            $similar->views++;
            $model->save($similar);

            return;
        }

        // Create a new visit record
        $model->save([
            'user_id' => $this->id,
            'visitor_id' => user_id() ?? null,
        ]);
    }
}
