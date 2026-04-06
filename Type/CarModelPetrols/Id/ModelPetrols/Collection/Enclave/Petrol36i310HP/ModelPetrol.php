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

namespace BaksDev\Reference\Car\Type\CarModelPetrols\Id\ModelPetrols\Collection\Enclave\Petrol36i310HP;

use BaksDev\Reference\Car\Type\CarModelPetrols\Id\ModelPetrols\CarModelPetrolInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;
use BaksDev\Reference\Car\Type\CarModels\Id\Models\Collection\Enclave as Model;
use BaksDev\Reference\Car\Type\CarModelGenerations\Id\ModelGenerations\Collection\IIFacelift as CarModelGeneration;

#[AutoconfigureTag('baks.car.model.petrols')]
final class ModelPetrol implements CarModelPetrolInterface
{
    /** Uid (ID) комплектации */
    public const string CAR_MODEL_PETROL_UID = '0198a2fa-ddc0-7eda-bc36-cf99bfa49836';

    /** Uid (ID) модели */
    public const string CAR_MODEL_UID = Model::CAR_MODEL_UID;

    /** Uid (ID) поколения */
    public const string CAR_MODEL_GENERATION_UID = CarModelGeneration::CAR_GENERATION_UID;

    /**
     * Возвращает UID комплектации
     */
    public static function getUid(): CarModelPetrolUid
    {
        return new CarModelPetrolUid(self::CAR_MODEL_PETROL_UID);
    }

    /**
     * Возвращает UID привязанной модели
     */
    public static function getModelUid(): CarModelUid
    {
        return new CarModelUid(self::CAR_MODEL_UID);
    }

    /**
     * Возвращает UID привязанного поколения
     */
    public static function getModelGenerationUid(): CarModelGenerationUid
    {
        return new CarModelGenerationUid(self::CAR_MODEL_GENERATION_UID);
    }

    /**
     * Сортировка (чем меньше число - тем первым в итерации будет значение)
     */
    public static function sort(): int
    {
        return 2;
    }
}
