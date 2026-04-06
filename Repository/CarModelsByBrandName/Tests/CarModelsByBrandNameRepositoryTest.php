<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelsByBrandName\Tests;

use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Reference\Car\Repository\CarModelsByBrandName\CarModelsByBrandNameInterface;
use BaksDev\Reference\Car\Type\CarBrands\Name\CarBrandName;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group reference-car
 */
#[Group('reference-car')]
#[When(env: 'test')]
class CarModelsByBrandNameRepositoryTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        /** @var CarModelsByBrandNameInterface $CarModelsByBrandNameRepository */
        $CarModelsByBrandNameRepository = self::getContainer()->get(CarModelsByBrandNameInterface::class);

        $result = $CarModelsByBrandNameRepository
            ->forBrandName(new CarBrandName('Acura'))
            ->findAll();

        self::assertInstanceOf(PaginatorInterface::class, $result);
    }

}