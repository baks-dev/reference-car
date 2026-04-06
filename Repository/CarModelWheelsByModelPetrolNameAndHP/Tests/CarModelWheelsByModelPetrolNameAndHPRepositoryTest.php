<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelWheelsByModelPetrolNameAndHP\Tests;

use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Reference\Car\Repository\CarModelWheelsByModelPetrolNameAndHP\CarModelWheelsByModelPetrolNameAndHPInterface;
use BaksDev\Reference\Car\Type\CarModelPetrols\HP\CarModelPetrolHP;
use BaksDev\Reference\Car\Type\CarModelPetrols\Name\CarModelPetrolName;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group reference-car
 */
#[Group('reference-car')]
#[When(env: 'test')]
class CarModelWheelsByModelPetrolNameAndHPRepositoryTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        /** @var CarModelWheelsByModelPetrolNameAndHPInterface $CarModelWheelsByModelPetrolNameAndHPRepository */
        $CarModelWheelsByModelPetrolNameAndHPRepository =
            self::getContainer()->get(CarModelWheelsByModelPetrolNameAndHPInterface::class);

        $result = $CarModelWheelsByModelPetrolNameAndHPRepository
            ->forModelPetrolNameAndHP(new CarModelPetrolName('1.5 VTEC'), new CarModelPetrolHP('200'))
            ->findAll();

        self::assertInstanceOf(PaginatorInterface::class, $result);
    }

}