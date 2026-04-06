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

namespace BaksDev\Reference\Car\Type\CarModelWheels\Id\ModelWheels\Collection\Tiggo5x;

use BaksDev\Reference\Car\Type\CarModelWheels\Id\ModelWheels\CarModelWheelsInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\ModelPetrols\Collection\Tiggo5x\Petrol15T148HP\ModelPetrol;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModelWheels\Id\CarModelWheelUid;

#[AutoconfigureTag('baks.car.model.wheels')]
final class OE22555R1898H implements CarModelWheelsInterface
{
    /** Uid (ID) колес */
    public const string CAR_MODEL_WHEEL_UID = '0198a7cf-034b-7363-8874-0c8c7ed397e9';

    /** Uid (ID) модели */
    public const string CAR_MODEL_PETROL_UID = ModelPetrol::CAR_MODEL_PETROL_UID;

    public static function getUid(): CarModelWheelUid
    {
        return new CarModelWheelUid(self::CAR_MODEL_WHEEL_UID);
    }

    public static function getModelPetrolUid(): CarModelPetrolUid
    {
        return new CarModelPetrolUid(self::CAR_MODEL_PETROL_UID);
    }

    public static function sort(): int
    {
        return 2;
    }
}
