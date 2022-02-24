<?php

namespace App\Tests;

use App\Entity\Team;
use App\Service\TeamManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TeamTest extends KernelTestCase
{
    public function testTeamInstance(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());
        /** @var TeamManager $teamManager */
        $teamManager = self::$container->get(TeamManager::class);

        $team = $teamManager->create();
        $team->setName('Cadiz Club de Futbol S.A.D.');
        $team->setBudget(22);
        $team = $teamManager->save($team);

        $this->assertInstanceOf(Team::class, $team);
    }
}
