<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\ExistCarModel\Tests;

use BaksDev\Reference\Car\Repository\ExistCarModel\ExistCarModelInterface;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group reference-car
 */
#[Group('reference-car')]
#[When(env: 'test')]
class ExistCarModelTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        /** @var ExistCarModelInterface $ExistCarModelRepository */
        $ExistCarModelRepository = self::getContainer()->get(ExistCarModelInterface::class);

        $existingCarModelId = new CarModelUid('0198560a-275d-7ead-bac8-f462687eb57d');
        $result = $ExistCarModelRepository->exist($existingCarModelId);
        self::assertTrue($result);
    }

}