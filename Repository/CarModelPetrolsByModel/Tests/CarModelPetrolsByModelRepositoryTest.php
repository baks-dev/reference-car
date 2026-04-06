<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelPetrolsByModel\Tests;

use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Reference\Car\Repository\CarModelPetrolsByModel\CarModelPetrolsByModelIdInterface;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;


/**
 * @group reference-car
 */
#[Group('reference-car')]
#[When(env: 'test')]
class CarModelPetrolsByModelRepositoryTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        /** @var CarModelPetrolsByModelIdInterface $CarModelPetrolsByModelIdRepository */
        $CarModelPetrolsByModelIdRepository = self::getContainer()->get(CarModelPetrolsByModelIdInterface::class);

        $result = $CarModelPetrolsByModelIdRepository
            ->forModel(new CarModelUid('0198560a-275d-7ead-bac8-f462687eb57d'))
            ->findAll();

        self::assertInstanceOf(PaginatorInterface::class, $result);
    }

}