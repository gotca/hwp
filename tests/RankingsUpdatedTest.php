<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RankingsUpdatedTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetMessage()
    {
        $lastRank = new App\Models\Rank(['rank' => 10, 'tied' => false]);
        $newRank = new App\Models\Rank(['rank' => 1, 'tied' => false]);

        $notification = new App\Notifications\RankingsUpdated($newRank, null);
        $this->assertEquals("New state rankings announced, we're 1st!", $notification->getMessage(), "no last week failed");

        $notification->newRank->rank = 2;
        $this->assertEquals("New state rankings announced, we're 2nd!", $notification->getMessage(), "ordinal 2nd failed");

        $notification->newRank->rank = 3;
        $this->assertEquals("New state rankings announced, we're 3rd!", $notification->getMessage(), "ordinal 3rd failed");

        $notification->newRank->rank = 8;
        $this->assertEquals("New state rankings announced, we're 8th!", $notification->getMessage(), "ordinal 8th failed");

        $notification->newRank->rank = 1;
        $notification->lastRank = $lastRank;
        $this->assertEquals("New state rankings announced, we moved up to 1st!", $notification->getMessage(), "move up to failed");

        $notification->lastRank->rank = 1;
        $notification->newRank->rank = 8;
        $this->assertEquals("New state rankings announced, we dropped to 8th.", $notification->getMessage(), "dropped to failed");

        $notification->lastRank->rank = $notification->newRank->rank = 1;
        $notification->lastRank->tied = $notification->newRank->tied = false;
        $this->assertEquals("New state rankings announced, we're still 1st!", $notification->getMessage(), "same no ties failed");

        $notification->lastRank->rank = $notification->newRank->rank = 1;
        $notification->lastRank->tied = false;
        $notification->newRank->tied = true;
        $this->assertEquals("New state rankings announced, we're tied for 1st!", $notification->getMessage(), "same new tied failed");

        $notification->lastRank->rank = $notification->newRank->rank = 1;
        $notification->lastRank->tied = true;
        $notification->newRank->tied = false;
        $this->assertEquals("New state rankings announced, we're 1st!", $notification->getMessage(), "same last tied failed");

        $notification->lastRank->rank = $notification->newRank->rank = 1;
        $notification->lastRank->tied = $notification->newRank->tied = true;
        $this->assertEquals("New state rankings announced, we're still tied for 1st!", $notification->getMessage(), "same both tied failed");
    }
}
