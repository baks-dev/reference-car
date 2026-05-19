<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
 *  
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Reference\Car\Type\CarModelWheels\ModelWheels;

use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModelWheels\Backspacing\CarModelWheelBackspacing;
use BaksDev\Reference\Car\Type\CarModelWheels\Bar\CarModelWheelBar;
use BaksDev\Reference\Car\Type\CarModelWheels\Diameter\CarModelWheelDiameter;
use BaksDev\Reference\Car\Type\CarModelWheels\Id\CarModelWheelUid;
use BaksDev\Reference\Car\Type\CarModelWheels\OffsetRange\CarModelWheelOffsetRange;
use BaksDev\Reference\Car\Type\CarModelWheels\Profile\CarModelWheelProfile;
use BaksDev\Reference\Car\Type\CarModelWheels\Rim\CarModelWheelRim;
use BaksDev\Reference\Car\Type\CarModelWheels\TireWeight\CarModelWheelTireWeight;
use BaksDev\Reference\Car\Type\CarModelWheels\Width\CarModelWheelWidth;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('baks.car.model.wheels')]
interface CarModelWheelsInterface
{
    public const string WHEEL_NAMESPACE = __NAMESPACE__.'\\';


    /** Отдает uid комплектации */
    public static function getUid(): CarModelWheelUid;


    public static function getBackspacingValue(): CarModelWheelBackspacing;

    public static function getBarValue(): CarModelWheelBar;

    public static function getDiameterValue(): CarModelWheelDiameter;

    public static function getOffsetRangeValue(): CarModelWheelOffsetRange;

    public static function getProfileValue(): CarModelWheelProfile;

    public static function getRimValue(): CarModelWheelRim;

    public static function getTireWeightValue(): CarModelWheelTireWeight;

    public static function getWidthValue(): CarModelWheelWidth;


    /** Отдает uid привязанной model petrol */
    public static function getModelPetrolUid(): CarModelPetrolUid;


    /**
     * Проверяет, относится ли строка к данному объекту
     */
    public static function equals(mixed $value): bool;


    /**
     * Сортировка (чем меньше число - тем первым в итерации будет значение)
     */
    public static function sort(): int;
}
