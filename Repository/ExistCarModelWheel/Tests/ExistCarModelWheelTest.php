<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\ExistCarModelWheel\Tests;

use BaksDev\Reference\Car\Repository\ExistCarModelWheel\ExistCarModelWheelInterface;
use BaksDev\Reference\Car\Type\CarModelWheels\Id\CarModelWheelUid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group reference-car
 */
#[Group('reference-car')]
#[When(env: 'test')]
class ExistCarModelWheelTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        /** @var ExistCarModelWheelInterface $ExistCarModelWheelRepository */
        $ExistCarModelWheelRepository = self::getContainer()->get(ExistCarModelWheelInterface::class);

        $existingCarModelWheel = new CarModelWheelUid('0198561f-fe14-7b33-b909-d5ebbec3195a');
        $result = $ExistCarModelWheelRepository->exist($existingCarModelWheel);

        self::assertTrue($result);
    }

}