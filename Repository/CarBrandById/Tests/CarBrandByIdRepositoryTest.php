<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarBrandById\Tests;

use BaksDev\Reference\Car\Repository\CarBrandById\CarBrandByIdInterface;
use BaksDev\Reference\Car\Repository\CarBrandById\CarBrandByIdResult;
use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;


/**
 * @group reference-car
 */
#[Group('reference-car')]
#[When(env: 'test')]
class CarBrandByIdRepositoryTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        /** @var CarBrandByIdInterface $CarBrandByIdRepository */
        $CarBrandByIdRepository = self::getContainer()->get(CarBrandByIdInterface::class);

        $CarBrandByIdResult = $CarBrandByIdRepository
            ->forBrand(new CarBrandUid('01985637-acba-7e07-bf7f-8598bc366a43'))
            ->find();

        // Вызываем все геттеры
        $reflectionClass = new ReflectionClass(CarBrandByIdResult::class);
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach($methods as $method)
        {
            // Методы без аргументов
            if($method->getNumberOfParameters() === 0)
            {
                // Вызываем метод
                $method->invoke($CarBrandByIdResult);
            }
        }

        self::assertTrue(true);
    }

}