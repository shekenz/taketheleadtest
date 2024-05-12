<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ComicAPIService;

class ComicAPIServiceTest extends TestCase
{
    public function test_total_count(): void
    {
        $count = ComicAPIService::fetchTotalCount();
        $this->assertIsInt($count);
        $this->assertGreaterThan(30000, $count);
    }
}
