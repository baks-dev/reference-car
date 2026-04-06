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

namespace BaksDev\Reference\Car\Type\CarModelWheels\Id\ModelWheels\Collection\RS5;

use BaksDev\Reference\Car\Type\CarModelWheels\Id\ModelWheels\CarModelWheelsInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\ModelPetrols\Collection\RS5\Petrol29TFSiquattro444HP\ModelPetrol;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModelWheels\Id\CarModelWheelUid;

#[AutoconfigureTag('baks.car.model.wheels')]
final class OE26535ZR1998Y implements CarModelWheelsInterface
{
    /** Uid (ID) колес */
    public const string CAR_MODEL_WHEEL_UID = '01989e0a-9687-7992-aa07-17721d387459';

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
