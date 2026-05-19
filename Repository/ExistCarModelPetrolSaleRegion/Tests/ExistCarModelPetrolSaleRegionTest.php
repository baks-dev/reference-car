<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\ExistCarModelPetrolSaleRegion\Tests;

use BaksDev\Reference\Car\Repository\ExistCarModelPetrolSaleRegion\ExistCarModelPetrolSaleRegionInterface;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModelPetrols\SaleRegion\ModelPetrols\Collection\Integra\Petrol15VTEC200HP\ModelPetrolSaleRegionCanada;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group reference-car
 */
#[Group('reference-car')]
#[When(env: 'test')]
class ExistCarModelPetrolSaleRegionTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        /** @var ExistCarModelPetrolSaleRegionInterface $ExistCarModelPetrolSaleRegionRepository */
        $ExistCarModelPetrolSaleRegionRepository = self::getContainer()->get(ExistCarModelPetrolSaleRegionInterface::class);

        $existingCarModelPetrol = new CarModelPetrolUid('0198560a-8d86-74a5-b8bb-e458a3f65304');
        $SaleRegion = new ModelPetrolSaleRegionCanada;
        $result = $ExistCarModelPetrolSaleRegionRepository->exist($existingCarModelPetrol, $SaleRegion);

        self::assertTrue(true);
    }

}