<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelById\Tests;

use BaksDev\Reference\Car\Repository\CarModelById\CarModelByIdInterface;
use BaksDev\Reference\Car\Repository\CarModelById\CarModelByIdResult;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;


/**
 * @group reference-car
 */
#[Group('reference-car')]
#[When(env: 'test')]
class CarModelByIdRepositoryTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        /** @var CarModelByIdInterface $CarModelByIdRepository */
        $CarModelByIdRepository = self::getContainer()->get(CarModelByIdInterface::class);

        $CarModelByIdResult = $CarModelByIdRepository
            ->forModel(new CarModelUid('0198560a-275d-7ead-bac8-f462687eb57d'))
            ->find();

        // Вызываем все геттеры
        $reflectionClass = new ReflectionClass(CarModelByIdResult::class);
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach($methods as $method)
        {
            // Методы без аргументов
            if($method->getNumberOfParameters() === 0)
            {
                // Вызываем метод
                $method->invoke($CarModelByIdResult);
            }
        }

        self::assertTrue(true);
    }

}