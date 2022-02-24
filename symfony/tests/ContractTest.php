<?php

namespace App\Tests;

use App\Entity\Contract;
use App\Entity\Trainer;
use App\Service\ContractManager;
use App\Service\PlayerManager;
use App\Service\TeamManager;
use App\Service\TrainerManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContractTest extends KernelTestCase
{
    public function testContractPlayerInstance(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());
        /** @var ContractManager $contractManager */
        $contractManager = self::$container->get(ContractManager::class);
        /** @var PlayerManager $playerManager */
        $playerManager = self::$container->get(PlayerManager::class);
        /** @var TeamManager $teamManager */
        $teamManager = self::$container->get(TeamManager::class);

        $now = new \DateTime();
        if ($now->format('m') > '06'){
            $year = intval($now->format('Y')) + 1;
            $endContractDate = new \DateTime($year . '-06-30');
        } else {
            $year = intval($now->format('Y'));
            $endContractDate = new \DateTime($year . '-06-30');
        }

        $team = $teamManager->create();
        $team->setName('Cadiz Club de Futbol S.A.D.');
        $team->setBudget(22);
        $team = $teamManager->save($team);

        $player = $playerManager->create();
        $player->setName('Juan Lotito Babsky');
        $player->setAge(30);
        $player = $playerManager->save($player);

        $contract = $contractManager->create();
        $contract->setTeam($team);
        $contract->setPlayer($player);
        $contract->setActive(true);
        $contract->setStartDate($now);
        $contract->setEndDate($endContractDate);
        $contract->setAmount(10);
        $contract = $contractManager->save($contract);

        $this->assertInstanceOf(Contract::class, $contract);
    }

    public function testContractTrainerInstance(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());
        /** @var ContractManager $contractManager */
        $contractManager = self::$container->get(ContractManager::class);
        /** @var TrainerManager $trainerManager */
        $trainerManager = self::$container->get(TrainerManager::class);
        /** @var TeamManager $teamManager */
        $teamManager = self::$container->get(TeamManager::class);

        $now = new \DateTime();
        if ($now->format('m') > '06'){
            $year = intval($now->format('Y')) + 1;
            $endContractDate = new \DateTime($year . '-06-30');
        } else {
            $year = intval($now->format('Y'));
            $endContractDate = new \DateTime($year . '-06-30');
        }

        $team = $teamManager->create();
        $team->setName('Cadiz Club de Futbol S.A.D.');
        $team->setBudget(22);
        $team = $teamManager->save($team);

        $trainer = $trainerManager->create();
        $trainer->setName('Luis Enrique');
        $trainer->setAge(48);
        $trainer = $trainerManager->save($trainer);

        $contract = $contractManager->create();
        $contract->setTeam($team);
        $contract->setTrainer($trainer);
        $contract->setActive(true);
        $contract->setStartDate($now);
        $contract->setEndDate($endContractDate);
        $contract->setAmount(10);
        $contract = $contractManager->save($contract);

        $this->assertInstanceOf(Contract::class, $contract);
        $this->assertInstanceOf(Trainer::class, $contract->getTrainer());
    }
}
