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

namespace BaksDev\Reference\Car\Type\CarModelPetrols\ModelPetrols\Collection;

use BaksDev\Reference\Car\Type\CarModelPetrols\ModelPetrols\CarModelPetrolInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;
use BaksDev\Reference\Car\Type\CarModelGenerations\ModelGenerations\Collection\AcuraIntegraIAV19861987 as Generation;
use BaksDev\Reference\Car\Type\CarModelPetrols\HP\CarModelPetrolHP;
use BaksDev\Reference\Car\Type\CarModelPetrols\KW\CarModelPetrolKW;
use BaksDev\Reference\Car\Type\CarModelPetrols\Name\CarModelPetrolName;
use BaksDev\Reference\Car\Type\CarModelPetrols\PS\CarModelPetrolPS;
use BaksDev\Reference\Car\Type\CarModelPetrols\Year\CarModelPetrolYear;

#[AutoconfigureTag('baks.car.model.petrols')]
final class AcuraIntegraIAV1986198716i113hp implements CarModelPetrolInterface
{
    /** Uid (ID) комплектации */
    public const string CAR_MODEL_PETROL_UID = '019e3b3c-615d-7dd5-8544-865e88894406';


    /** Значение названия комплектации */
    public const string CAR_MODEL_PETROL_VALUE = '16i113hp';


    /** Значение HP */
    public const string CAR_HP_VALUE = '118';


    /** Значение KW */
    public const string CAR_KW_VALUE = '88';


    /** Значение PS */
    public const string CAR_PS_VALUE = '120';


    /** Значение Year */
    public const string CAR_YEAR_VALUE = 'USA+';


    /** @var string[] Список для фильтрации */
    public const array HAYSTACK = [self::CAR_MODEL_PETROL_VALUE, self::CAR_MODEL_PETROL_UID];


    /** Uid (ID) поколения */
    public const string CAR_MODEL_GENERATION_UID = Generation::CAR_GENERATION_UID;


    /**
    * Возвращает UID комплектации
    */
    public static function getUid(): CarModelPetrolUid
    {
        return new CarModelPetrolUid(self::CAR_MODEL_PETROL_UID);
    }


    /**
    * Возвращает значение (value) HP
    */
    public static function getHPValue(): CarModelPetrolHP
    {
        return new CarModelPetrolHP(self::CAR_HP_VALUE);
    }


    /**
    * Возвращает значение (value) KW
    */
    public static function getKWValue(): CarModelPetrolKW
    {
        return new CarModelPetrolKW(self::CAR_KW_VALUE);
    }


    /**
    * Возвращает значение названия комплектации
    */
    public static function getNameValue(): CarModelPetrolName
    {
        return new CarModelPetrolName(self::CAR_MODEL_PETROL_VALUE);
    }


    /**
    * Возвращает значение (value) PS
    */
    public static function getPSValue(): CarModelPetrolPS
    {
        return new CarModelPetrolPS(self::CAR_PS_VALUE);
    }


    /**
    * Возвращает значение (value) PetrolYear
    */
    public static function getPetrolYearValue(): CarModelPetrolYear
    {
        return new CarModelPetrolYear(self::CAR_YEAR_VALUE);
    }


    /**
    * Возвращает UID привязанного поколения
    */
    public static function getModelGenerationUid(): CarModelGenerationUid
    {
        return new CarModelGenerationUid(self::CAR_MODEL_GENERATION_UID);
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


    /**
    * Сортировка (чем меньше число - тем первым в итерации будет значение)
    */
    public static function sort(): int
    {
        return 2;
    }
}
