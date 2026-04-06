<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelPetrolById\Tests;

use BaksDev\Reference\Car\Repository\CarModelPetrolById\CarModelPetrolByIdInterface;
use BaksDev\Reference\Car\Repository\CarModelPetrolById\CarModelPetrolByIdResult;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;


/**
 * @group reference-car
 */
#[Group('reference-car')]
#[When(env: 'test')]
class CarModelPetrolByIdRepositoryTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        /** @var CarModelPetrolByIdInterface $CarModelPetrolByIdRepository */
        $CarModelPetrolByIdRepository = self::getContainer()->get(CarModelPetrolByIdInterface::class);

        $CarModelPetrolByIdResult = $CarModelPetrolByIdRepository
            ->forModelPetrol(new CarModelPetrolUid('0198560a-8d86-74a5-b8bb-e458a3f65304'))
            ->find();

        // Вызываем все геттеры
        $reflectionClass = new ReflectionClass(CarModelPetrolByIdResult::class);
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach($methods as $method)
        {
            // Методы без аргументов
            if($method->getNumberOfParameters() === 0)
            {
                // Вызываем метод
                $method->invoke($CarModelPetrolByIdResult);
            }
        }

        self::assertTrue(true);
    }

}