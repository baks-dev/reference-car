<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelByName\Tests;

use BaksDev\Reference\Car\Repository\CarModelById\CarModelByIdResult;
use BaksDev\Reference\Car\Repository\CarModelByName\CarModelByNameInterface;
use BaksDev\Reference\Car\Repository\CarModelByName\CarModelByNameResult;
use BaksDev\Reference\Car\Type\CarModels\Name\CarModelName;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;


/**
 * @group reference-car
 */
#[Group('reference-car')]
#[When(env: 'test')]
class CarModelByNameRepositoryTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        /** @var CarModelByNameInterface $CarModelByNameRepository */
        $CarModelByNameRepository = self::getContainer()->get(CarModelByNameInterface::class);

        $CarModelByNameResult = $CarModelByNameRepository
            ->forModelName(new CarModelName('Integra'))
            ->find();

        if(false === ($CarModelByNameResult instanceof CarModelByIdResult))
        {
            self::assertFalse(false);
            return;
        }

        // Вызываем все геттеры
        $reflectionClass = new ReflectionClass(CarModelByNameResult::class);
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach($methods as $method)
        {
            // Методы без аргументов
            if($method->getNumberOfParameters() === 0)
            {
                // Вызываем метод
                $method->invoke($CarModelByNameResult);
            }
        }

        self::assertTrue(true);
    }

}