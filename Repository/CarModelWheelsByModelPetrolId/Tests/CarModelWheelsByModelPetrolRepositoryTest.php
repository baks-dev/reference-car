<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelWheelsByModelPetrolId\Tests;

use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Reference\Car\Repository\CarModelWheelsByModelPetrolId\CarModelWheelsByModelPetrolIdInterface;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group reference-car
 */
#[Group('reference-car')]
#[When(env: 'test')]
class CarModelWheelsByModelPetrolRepositoryTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        /** @var CarModelWheelsByModelPetrolIdInterface $CarModelWheelsByModelPetrolIdRepository */
        $CarModelWheelsByModelPetrolIdRepository = self::getContainer()->get(CarModelWheelsByModelPetrolIdInterface::class);

        $result = $CarModelWheelsByModelPetrolIdRepository
            ->forModelPetrol(new CarModelPetrolUid('0198560a-8d86-74a5-b8bb-e458a3f65304'))
            ->findAll();

        self::assertInstanceOf(PaginatorInterface::class, $result);
    }

}