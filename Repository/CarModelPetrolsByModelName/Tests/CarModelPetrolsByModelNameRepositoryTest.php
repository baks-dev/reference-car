<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelPetrolsByModelName\Tests;

use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Reference\Car\Repository\CarModelPetrolsByModelName\CarModelPetrolsByModelNameInterface;
use BaksDev\Reference\Car\Type\CarModels\Name\CarModelName;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[Group('reference-car')]
#[When(env: 'test')]
class CarModelPetrolsByModelNameRepositoryTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        /** @var CarModelPetrolsByModelNameInterface $CarModelPetrolsByModelNameRepository */
        $CarModelPetrolsByModelNameRepository = self::getContainer()->get(CarModelPetrolsByModelNameInterface::class);

        $result = $CarModelPetrolsByModelNameRepository
            ->forModelName(new CarModelName('Integra'))
            ->findAll();

        self::assertInstanceOf(PaginatorInterface::class, $result);
    }

}