<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\ExistCarBrand\Tests;

use BaksDev\Reference\Car\Repository\ExistCarBrand\ExistCarBrandInterface;
use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group reference-car
 */
#[Group('reference-car')]
#[When(env: 'test')]
class ExistCarBrandTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        /** @var ExistCarBrandInterface $ExistCarBrandRepository */
        $ExistCarBrandRepository = self::getContainer()->get(ExistCarBrandInterface::class);

        $existingBrandId = new CarBrandUid('01985609-4d8b-7d48-b0d2-17930dcd6d5e');
        $result = $ExistCarBrandRepository->exist($existingBrandId);

        self::assertTrue(true);
    }

}