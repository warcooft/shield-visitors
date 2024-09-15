<?php

namespace Aselsan\Visitors\Models;

use Aselsan\Visitors\Config\Visitors;
use Aselsan\Visitors\Entities\Visitor;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class VisitorModel extends Model
{
    protected $table         = 'users_visitors';
    protected $primaryKey    = 'id';
    protected $returnType    = Visitor::class;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id',
        'visitor_id',
        'ip_address',
        'user_agent',
        'views',
        'scheme',
        'host',
        'port',
        'user',
        'pass',
        'path',
        'query',
        'fragment',
    ];

    protected $validationRules = [
        'user_id'    => 'required',
        'visitor_id' => 'required',
        'host'       => 'required',
        'path'       => 'required',
    ];

    /**
     * Parses the current URL and adds relevant
     * Request info to create an Visit.
     */
    public function makeFromRequest(IncomingRequest $request): Visitor
    {
        // Get the URI of the current Request
        $uri = current_url(true, $request);

        /**
         * Only try to identify a current user if the appropriate helper is defined
         *
         * @see https://codeigniter4.github.io/CodeIgniter4/extending/authentication.html
         */
        $userId = function_exists('user_id') ? user_id() : null;

        return new Visitor([
            'user_id'    => $userId,
            'visitor_id' => user_id(),
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

    /**
     * Finds the first visit with similar characteristics
     * based on the configuration settings.
     */
    public function findSimilar(Visitor $visit): ?Visitor
    {
        $config   = config(Visitors::class);
        $tracking = $visit->toRawArray()[$config->trackingMethod] ?? null;

        // Required fields
        if (empty($tracking) || empty($visit->host) || empty($visit->path)) {
            return null;
        }

        // Check for matching components within the configured period
        $since = Time::now()->subSeconds($config->resetAfter)->format('Y-m-d H:i:s');

        return $this->where('host', $visit->host)
            ->where('path', $visit->path)
            ->where('query', (string) $visit->query)
            ->where($config->trackingMethod, $tracking)
            ->where('created_at >=', $since)
            ->first();
    }
}
