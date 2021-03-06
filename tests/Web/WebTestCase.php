<?php

namespace App\Tests\Web;

use App\Entity\GoalTimes;
use App\Entity\Positions;
use App\Entity\Prediction;
use App\Entity\Season;
use App\Repository\FixtureList;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
    protected function login(): KernelBrowser
    {
        $client = WebTestCase::createClient();
        $container = $client->getContainer();
        $em = $container->get('doctrine.orm.default_entity_manager');
        $oldSeason = (new Season())
            ->setLabel('Old season')
            ->setStartDate((new \DateTimeImmutable())->sub(new \DateInterval('P1Y')))
            ->setEndDate((new \DateTimeImmutable())->sub(new \DateInterval('P1M')))
        ;
        $em->persist($oldSeason);
        $season = (new Season())
            ->setLabel('Current season')
            ->setStartDate((new \DateTimeImmutable())->sub(new \DateInterval('P1M')))
            ->setEndDate((new \DateTimeImmutable())->add(new \DateInterval('P1M')))
        ;
        $em->persist($season);
        $em->flush();
        $login = $client->request('GET', '/login');
        $form = $login->selectButton('Sign in')->form();

        $form['username'] = 'Andy';
        $form['password'] = 'whing';

        $client->submit($form);

        return $client;
    }

    protected function addMatch(KernelBrowser $client): KernelBrowser
    {
        $index = $client->request('GET', '/');
        $link = $index
            ->filter('a:contains("Add match")')
            ->eq(0)
            ->link();
        $match = $client->click($link);
        $form = $match->selectButton('Add')->form();

        $form['match[opponent]'] = $opposition = uniqid('team');
        $form['match[date]'] = date('Y-m-d');
        $form['match[location]'] = 'Home';
        $form['match[competition]'] = 'League';

        $client->submit($form);

        return $client;
    }

    protected function addPrediction(KernelBrowser $client, $user, $position = null, $timing = null)
    {
        $position = $position ?? Positions::DEFENDERS()->getValue();
        $timing = $timing ?? GoalTimes::SECOND_HALF()->getValue();
        $container = $client->getContainer();
        $fixtureList = $container->get(FixtureList::class);
        $em = $container->get('doctrine.orm.entity_manager');
        $prediction = (new Prediction())
            ->setUser($user)
            ->setPosition($position)
            ->setTime($timing)
            ->setMatch($fixtureList->findNextMatch())
            ->setAtMatch(true)
            ->setNiceTime('Yes');
        $em->persist($prediction);
        $em->flush();
    }
}
