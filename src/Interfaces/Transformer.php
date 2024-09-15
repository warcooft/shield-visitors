<?php

namespace Aselsan\Visitors\Interfaces;

use Aselsan\Visitors\Entities\Visitor;
use CodeIgniter\HTTP\IncomingRequest;

interface Transformer
{
    /**
     * Returns the updated Visit, or `null` to cancel recording.
     */
    public static function transform(Visitor $visit, IncomingRequest $request): ?Visitor;
}
