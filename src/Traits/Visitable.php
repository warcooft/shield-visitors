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

        // Get the URI of the current Request
        $uri     = current_url(true);
        $request = service('request');

        // Create a new visit record
        $model->save([
            'user_id' => $this->id,
            'visitor_id' => user_id() ?? null,
            'scheme'     => $uri->getScheme(),
            'host'       => $uri->getHost(),
            'port'       => $uri->getPort() ?? '',
            'user'       => $uri->showPassword(false)->getUserInfo() ?? '',
            'path'       => $uri->getPath(),
            'query'      => $uri->getQuery(),
            'fragment'   => $uri->getFragment(),
            'user_agent' => $request->getServer('HTTP_USER_AGENT') ?? '',
            'ip_address' => $request->getServer('REMOTE_ADDR'),
        ]);
    }
}
