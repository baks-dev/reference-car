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

namespace BaksDev\Reference\Car\Type\CarModelPetrols\ModelPetrols;

use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;
use BaksDev\Reference\Car\Type\CarModelPetrols\HP\CarModelPetrolHP;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModelPetrols\KW\CarModelPetrolKW;
use BaksDev\Reference\Car\Type\CarModelPetrols\Name\CarModelPetrolName;
use BaksDev\Reference\Car\Type\CarModelPetrols\PS\CarModelPetrolPS;
use BaksDev\Reference\Car\Type\CarModelPetrols\Year\CarModelPetrolYear;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('baks.car.model.petrols')]
interface CarModelPetrolInterface
{
    public const string MODEL_PETROL_NAMESPACE = __NAMESPACE__.'\\';

    /** Отдает uid комплектации */
    public static function getUid(): CarModelPetrolUid;


    /**
     * Возвращает значение (value) HP
     */
    public static function getHPValue(): CarModelPetrolHP;


    /**
     * Возвращает значение (value) KW
     */
    public static function getKWValue(): CarModelPetrolKW;


    /**
     * Возвращает значение (value) Name
     */
    public static function getNameValue(): CarModelPetrolName;


    /**
     * Возвращает значение (value) PS
     */
    public static function getPSValue(): CarModelPetrolPS;


    /**
     * Возвращает значение (value) PetrolYear
     */
    public static function getPetrolYearValue(): CarModelPetrolYear;


    /**
     * Возвращает UID привязанного поколения
     */
    public static function getModelGenerationUid(): CarModelGenerationUid;


    /**
     * Сортировка (чем меньше число - тем первым в итерации будет значение)
     */
    public static function sort(): int;
}
