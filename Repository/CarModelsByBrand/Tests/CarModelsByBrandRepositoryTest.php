<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelsByBrand\Tests;

use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Reference\Car\Repository\CarModelsByBrand\CarModelsByBrandIdInterface;
use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group reference-car
 */
#[Group('reference-car')]
#[When(env: 'test')]
class CarModelsByBrandRepositoryTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        /** @var CarModelsByBrandIdInterface $CarModelsByBrandRepository */
        $CarModelsByBrandRepository = self::getContainer()->get(CarModelsByBrandIdInterface::class);

        $result = $CarModelsByBrandRepository
            ->forBrand(new CarBrandUid('01985609-4d8b-7d48-b0d2-17930dcd6d5e'))
            ->findAll();

        self::assertInstanceOf(PaginatorInterface::class, $result);
    }

}