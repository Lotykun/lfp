<?php

namespace App\Tests;

use App\Entity\Trainer;
use App\Service\TrainerManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TrainerTest extends KernelTestCase
{
    public function testTrainerInstance(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());
        /** @var TrainerManager $trainerManager */
        $trainerManager = self::$container->get(TrainerManager::class);

        $trainer = $trainerManager->create();
        $trainer->setName('Luis Enrique');
        $trainer->setAge(48);
        $trainer = $trainerManager->save($trainer);

        $this->assertInstanceOf(Trainer::class, $trainer);
    }
}
