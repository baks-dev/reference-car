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

namespace BaksDev\Reference\Car\Type\CarModelGenerations\Id\ModelGenerations\Collection;

use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;
use BaksDev\Reference\Car\Type\CarModelGenerations\Id\ModelGenerations\CarModelGenerationsInterface;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use BaksDev\Reference\Car\Type\CarModels\Id\Models\Collection\M6 as Model;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('baks.car.generations')]
final class F06F12F13Facelift implements CarModelGenerationsInterface
{
    /** Uid (ID) поколения */
    public const CAR_GENERATION_UID = '0198a2f0-eeb4-7e8a-95e5-5fa5b65b34e1';

    /** Uid (ID) модели */
    public const string CAR_MODEL_UID = Model::CAR_MODEL_UID;

    public static function getUid(): CarModelGenerationUid
    {
        return new CarModelGenerationUid(self::CAR_GENERATION_UID);
    }

    /**
     * Возвращает UID привязанной модели
     */
    public static function getModelUid(): CarModelUid
    {
        return new CarModelUid(self::CAR_MODEL_UID);
    }

    public static function sort(): int
    {
        return 2;
    }
}
