<?php

namespace App\Tests\Web;

use App\Entity\GoalTimes;
use App\Entity\Positions;

class UserTest extends WebTestCase
{
    public function testViewPageForSpecificUser()
    {
        $client = $this->login();
        $client->request('GET', '/user/Andy');
        $this->assertContains('All predictions by Andy', $client->getResponse()->getContent());
    }

    public function testUserPageShowsPredictions()
    {
        $client = $this->login();
        $this->addMatch($client);
        $position = Positions::STRIKERS()->getValue();
        $timing = GoalTimes::FIFTH_FIFTEEN()->getValue();
        $this->addPrediction($client, 'Andy', $position, $timing);
        $scores = $client->request('GET', '/user/Andy');
        $this->assertGreaterThanOrEqual(1, $scores->filter('li.prediction')->count());
        $this->assertContains($position, $client->getResponse()->getContent());
        $this->assertContains($timing, $client->getResponse()->getContent());
    }

    public function testUserPageShowsScoresForPreviousSeason()
    {
        $client = $this->login();
        $scores = $client->request('GET', '/7/user/Andy');
        $this->assertEquals(48, $scores->filter('li.prediction')->count());
    }
}
