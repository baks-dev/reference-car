<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\ExistCarModelPetrolYear\Tests;

use BaksDev\Reference\Car\Repository\ExistCarModelPetrolYear\ExistCarModelPetrolYearInterface;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModelPetrols\Year\ModelPetrols\Collection\Integra\Petrol15VTEC200HP\ModelPetrolYear2022;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group reference-car
 */
#[Group('reference-car')]
#[When(env: 'test')]
class ExistCarModelPetrolYearTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        /** @var ExistCarModelPetrolYearInterface $ExistCarModelPetrolYearRepository */
        $ExistCarModelPetrolYearRepository = self::getContainer()->get(ExistCarModelPetrolYearInterface::class);

        $existingCarModelPetrol = new CarModelPetrolUid('0198560a-8d86-74a5-b8bb-e458a3f65304');
        $Year = new ModelPetrolYear2022;
        $result = $ExistCarModelPetrolYearRepository->exist($existingCarModelPetrol, $Year);
        self::assertTrue($result);
    }

}