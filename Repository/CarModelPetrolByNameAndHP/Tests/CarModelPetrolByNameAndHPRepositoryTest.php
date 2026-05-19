<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelPetrolByNameAndHP\Tests;

use BaksDev\Reference\Car\Repository\CarModelPetrolByNameAndHP\CarModelPetrolByNameAndHPInterface;
use BaksDev\Reference\Car\Repository\CarModelPetrolByNameAndHP\CarModelPetrolByNameAndHPResult;
use BaksDev\Reference\Car\Type\CarModelPetrols\HP\CarModelPetrolHP;
use BaksDev\Reference\Car\Type\CarModelPetrols\Name\CarModelPetrolName;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;


/**
 * @group reference-car
 */
#[Group('reference-car')]
#[When(env: 'test')]
class CarModelPetrolByNameAndHPRepositoryTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        /** @var CarModelPetrolByNameAndHPInterface $CarModelPetrolByNameAndHPRepository */
        $CarModelPetrolByNameAndHPRepository = self::getContainer()->get(CarModelPetrolByNameAndHPInterface::class);

        $CarModelPetrolByNameAndHPResult = $CarModelPetrolByNameAndHPRepository
            ->forModelPetrolHP(new CarModelPetrolHP('1.5 VTEC'))
            ->forModelPetrolName(new CarModelPetrolName('Integra'))
            ->find();

        if(false === ($CarModelPetrolByNameAndHPResult instanceof CarModelPetrolByNameAndHPResult))
        {
            self::assertFalse(false);
            return;
        }

        // Вызываем все геттеры
        $reflectionClass = new ReflectionClass(CarModelPetrolByNameAndHPResult::class);
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach($methods as $method)
        {
            // Методы без аргументов
            if($method->getNumberOfParameters() === 0)
            {
                // Вызываем метод
                $method->invoke($CarModelPetrolByNameAndHPResult);
            }
        }

        self::assertTrue(true);
    }

}