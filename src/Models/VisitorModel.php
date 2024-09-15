<?php

namespace Aselsan\Visitors\Models;

use App\Models\UserModel;
use Aselsan\Visitors\Config\Visitors;
use Aselsan\Visitors\Entities\Visitor;
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
        // 'host'       => 'required',
        // 'path'       => 'required',
    ];

    /**
     * Finds the first visit with similar characteristics
     * based on the configuration settings.
     */
    public function findSimilar($user_id): ?Visitor
    {
        $config   = config(Visitors::class);

        // Check for matching components within the configured period
        $since = Time::now()->subSeconds($config->resetAfter)->format('Y-m-d H:i:s');

        return $this->where('visitor_id', user_id())
            ->where('user_id', $user_id)
            ->where('created_at >=', $since)
            ->first();
    }

    /**
     * Get visitor for one user.
     */
    public function getById(int $user_id): array
    {
        $visitorIds = $this->builder()
            ->select('visitor_id')
            ->where('user_id', $user_id)
            ->get()
            ->getResultArray();

        if (empty($visitorIds)) {
            return [];
        }

        $visitorIds = array_map('intval', array_column($visitorIds, 'visitor_id'));

        return $visitorIds;
    }

    /**
     * Get visitor for many user.
     */
    public function getByIds(array $user_ids): array
    {
        $visitorIds = $this->builder()
            ->select('visitor_id, user_id')
            ->distinct()
            ->whereIn('user_id', $user_ids)
            ->get()
            ->getResultArray();

        if (empty($visitorIds)) {
            return [];
        }


        $visitorMaster = [];

        foreach ($visitorIds as $tag) {
            $visitorMaster[$tag['user_id']][] = $tag['visitor_id'];
        }

        $visitorIds = array_map('intval', array_unique(array_column($visitorIds, 'visitor_id')));

        $model = model(UserModel::class);
        $visitors   = $model->find($visitorIds);
        $visitors   = array_column($visitors, null, 'id');

        $results = [];

        foreach ($visitorMaster as $visit => $visitor_id) {
            foreach ($visitor_id as $vid) {
                if (isset($visitors[$vid])) {
                    $results[$visit][] = $visitors[$vid];
                }
            }
        }

        return $results;
    }
}
