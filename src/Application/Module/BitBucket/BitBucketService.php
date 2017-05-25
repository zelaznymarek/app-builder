<?php

declare(strict_types=1);

namespace Pvg\Application\Module\BitBucket;

interface BitBucketService
{
    public function fetchBitBucketData(int $id) : void;
}
