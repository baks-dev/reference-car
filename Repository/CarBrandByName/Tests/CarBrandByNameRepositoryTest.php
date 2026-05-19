<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarBrandByName\Tests;

use BaksDev\Reference\Car\Repository\CarBrandByName\CarBrandByNameInterface;
use BaksDev\Reference\Car\Repository\CarBrandByName\CarBrandByNameResult;
use BaksDev\Reference\Car\Type\CarBrands\Name\CarBrandName;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;


/**
 * @group reference-car
 */
#[Group('reference-car')]
#[When(env: 'test')]
class CarBrandByNameRepositoryTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        /** @var CarBrandByNameInterface $CarBrandByNameRepository */
        $CarBrandByNameRepository = self::getContainer()->get(CarBrandByNameInterface::class);

        $CarBrandByNameResult = $CarBrandByNameRepository
            ->forBrandName(new CarBrandName('Acura'))
            ->find();

        if(false === ($CarBrandByNameResult instanceof CarBrandByNameResult))
        {
            self::assertFalse(false);
            return;
        }

        // Вызываем все геттеры
        $reflectionClass = new ReflectionClass(CarBrandByNameResult::class);
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach($methods as $method)
        {
            // Методы без аргументов
            if($method->getNumberOfParameters() === 0)
            {
                // Вызываем метод
                $method->invoke($CarBrandByNameResult);
            }
        }

        self::assertTrue(true);
    }
}
