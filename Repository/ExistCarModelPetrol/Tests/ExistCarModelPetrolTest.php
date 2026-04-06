<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\ExistCarModelPetrol\Tests;

use BaksDev\Reference\Car\Repository\ExistCarModelGeneration\ExistCarModelGenerationInterface;
use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group reference-car
 */
#[Group('reference-car')]
#[When(env: 'test')]
class ExistCarModelPetrolTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        /** @var ExistCarModelGenerationInterface $ExistCarModelGenerationRepository */
        $ExistCarModelGenerationRepository = self::getContainer()->get(ExistCarModelGenerationInterface::class);

        $existingCarModelGenerationId = new CarModelGenerationUid('01985638-b27e-77bc-9145-e6c5b2f90155');
        $result = $ExistCarModelGenerationRepository->exist($existingCarModelGenerationId);
        self::assertTrue($result);
    }

}