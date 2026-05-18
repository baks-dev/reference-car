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

namespace BaksDev\Reference\Car\Type\CarModelWheels\ModelWheels\Collection;

use BaksDev\Reference\Car\Type\CarModelWheels\ModelWheels\CarModelWheelsInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use BaksDev\Reference\Car\Type\CarModelPetrols\ModelPetrols\Collection\AcuraIntegraIIIDBDC1994199518i142hp as ModelPetrol;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModelWheels\Id\CarModelWheelUid;
use BaksDev\Reference\Car\Type\CarModelWheels\Backspacing\CarModelWheelBackspacing;
use BaksDev\Reference\Car\Type\CarModelWheels\Bar\CarModelWheelBar;
use BaksDev\Reference\Car\Type\CarModelWheels\Diameter\CarModelWheelDiameter;
use BaksDev\Reference\Car\Type\CarModelWheels\OffsetRange\CarModelWheelOffsetRange;
use BaksDev\Reference\Car\Type\CarModelWheels\Profile\CarModelWheelProfile;
use BaksDev\Reference\Car\Type\CarModelWheels\Rim\CarModelWheelRim;
use BaksDev\Reference\Car\Type\CarModelWheels\TireWeight\CarModelWheelTireWeight;
use BaksDev\Reference\Car\Type\CarModelWheels\Width\CarModelWheelWidth;


#[AutoconfigureTag('baks.car.model.wheels')]
final class AcuraIntegraIIIDBDC1994199518i142hpOE19560R1485V implements CarModelWheelsInterface
{
    /** Uid (ID) колес */
    public const string CAR_MODEL_WHEEL_UID = '019e3b35-983b-7047-a05a-ee2b8407cf8a';


    /** Значение колес */
    public const string CAR_MODEL_WHEEL_VALUE = 'OE 195/60R14 85V';


    /** Значение Возврата */
    public const string CAR_MODEL_WHEEL_BACKSPACING_VALUE = '128';


    /** Значение Давления */
    public const string CAR_MODEL_WHEEL_BAR_VALUE = '';


    /** Значение диамтра */
    public const string CAR_MODEL_WHEEL_DIAMETER_VALUE = '14';


    /** Значение Диапазона смещения */
    public const string CAR_MODEL_WHEEL_OFFSET_RANGE_VALUE = '43 - 47';


    /** Значение профиля колеса */
    public const string CAR_MODEL_WHEEL_PROFILE_VALUE = '60';


    /** Значение обода колеса */
    public const string CAR_MODEL_WHEEL_RIM_VALUE = '5.5JJx14 ET45';


    /** Значение веса колеса */
    public const string CAR_MODEL_WHEEL_TIRE_WEIGHT_VALUE = '8.2';


    /** Значение ширины колеса */
    public const string CAR_MODEL_WHEEL_WIDTH_VALUE = '195';


    /** @var string[] Список для фильтрации */
    public const array HAYSTACK = [self::CAR_MODEL_WHEEL_VALUE, self::CAR_MODEL_WHEEL_UID];


    /** Uid (ID) комплектации */
    public const string CAR_MODEL_PETROL_UID = ModelPetrol::CAR_MODEL_PETROL_UID;


    public static function getUid(): CarModelWheelUid
    {
        return new CarModelWheelUid(self::CAR_MODEL_WHEEL_UID);
    }

    public static function getBackspacingValue(): CarModelWheelBackspacing
    {
        return new CarModelWheelBackspacing(self::CAR_MODEL_WHEEL_BACKSPACING_VALUE);
    }

    public static function getBarValue(): CarModelWheelBar
    {
        return new CarModelWheelBar(self::CAR_MODEL_WHEEL_BAR_VALUE);
    }

    public static function getDiameterValue(): CarModelWheelDiameter
    {
        return new CarModelWheelDiameter(self::CAR_MODEL_WHEEL_DIAMETER_VALUE);
    }

    public static function getOffsetRangeValue(): CarModelWheelOffsetRange
    {
        return new CarModelWheelOffsetRange(self::CAR_MODEL_WHEEL_OFFSET_RANGE_VALUE);
    }

    public static function getProfileValue(): CarModelWheelProfile
    {
        return new CarModelWheelProfile(self::CAR_MODEL_WHEEL_PROFILE_VALUE);
    }

    public static function getRimValue(): CarModelWheelRim
    {
        return new CarModelWheelRim(self::CAR_MODEL_WHEEL_RIM_VALUE);
    }

    public static function getTireWeightValue(): CarModelWheelTireWeight
    {
        return new CarModelWheelTireWeight(self::CAR_MODEL_WHEEL_TIRE_WEIGHT_VALUE);
    }

    public static function getWidthValue(): CarModelWheelWidth
    {
        return new CarModelWheelWidth(self::CAR_MODEL_WHEEL_WIDTH_VALUE);
    }

    public static function getModelPetrolUid(): CarModelPetrolUid
    {
        return new CarModelPetrolUid(self::CAR_MODEL_PETROL_UID);
    }

    public static function equals(mixed $value): bool
    {
        if (is_object($value) && method_exists($value, '__toString'))
        {
            $value = (string)$value;
        }

        if (is_string($value))
        {
            return array_any(self::HAYSTACK, static fn($item) => (mb_strtolower($value) === mb_strtolower($item)));
        }

        return false;
    }

    public static function sort(): int
    {
        return 2;
    }
}
