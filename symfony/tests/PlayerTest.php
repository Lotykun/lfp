<?php

namespace App\Tests;

use App\Entity\Player;
use App\Service\PlayerManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PlayerTest extends KernelTestCase
{
    /*public function testSomething(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());
        //$routerService = self::$container->get('router');
        //$myCustomService = self::$container->get(CustomService::class);
    }*/

    public function testPlayer(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());
        /** @var PlayerManager $playerManager */
        $playerManager = self::$container->get(PlayerManager::class);
        $player = $playerManager->create();

        $player->setName('Juan Lotito Babsky');
        $player->setAge(30);
        $player = $playerManager->save($player);

        $this->assertInstanceOf(Player::class, $player);
    }
}
