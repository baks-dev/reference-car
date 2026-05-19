<?php
/*
 * Copyright 2026.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Reference\Car\Repository\CarModelWheelsByModelPetrolUrl;

use BaksDev\Reference\Car\Type\CarModelPetrols\HP\CarModelPetrolHP;
use BaksDev\Reference\Car\Type\CarModelWheels\Bar\CarModelWheelBar;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModelPetrols\Name\CarModelPetrolName;
use BaksDev\Reference\Car\Type\CarModelWheels\Backspacing\CarModelWheelBackspacing;
use BaksDev\Reference\Car\Type\CarModelWheels\Diameter\CarModelWheelDiameter;
use BaksDev\Reference\Car\Type\CarModelWheels\Id\CarModelWheelUid;
use BaksDev\Reference\Car\Type\CarModelWheels\OffsetRange\CarModelWheelOffsetRange;
use BaksDev\Reference\Car\Type\CarModelWheels\Profile\CarModelWheelProfile;
use BaksDev\Reference\Car\Type\CarModelWheels\Rim\CarModelWheelRim;
use BaksDev\Reference\Car\Type\CarModelWheels\TireWeight\CarModelWheelTireWeight;
use BaksDev\Reference\Car\Type\CarModelWheels\Width\CarModelWheelWidth;

final readonly class CarModelWheelsByModelPetrolUrlResult
{
    public function __construct(
        private string $id,
        private string $diameter,
        private string $profile,
        private string $width,
        private string $rim,
        private ?string $offset_range,
        private ?string $bar,
        private string $tire_weight,
        private ?string $backspacing,
        private string $petrol_id,
        private string $petrol_name,
        private string $petrol_hp,
    ) {}

    public function getId(): CarModelWheelUid
    {
        return new CarModelWheelUid($this->id);
    }

    public function getDiameter(): CarModelWheelDiameter
    {
        return new CarModelWheelDiameter($this->diameter);
    }

    public function getProfile(): CarModelWheelProfile
    {
        return new CarModelWheelProfile($this->profile);
    }

    public function getWidth(): CarModelWheelWidth
    {
        return new CarModelWheelWidth($this->width);
    }

    public function getRim(): CarModelWheelRim
    {
        return new CarModelWheelRim($this->rim);
    }

    public function getOffsetRange(): ?CarModelWheelOffsetRange
    {
        return false === empty($this->offset_range) ? new CarModelWheelOffsetRange($this->offset_range) : null;
    }

    public function getBar(): ?CarModelWheelBar
    {
        return false === empty($this->bar) ? new CarModelWheelBar($this->bar) : null;
    }

    public function getTireWeight(): CarModelWheelTireWeight
    {
        return new CarModelWheelTireWeight($this->tire_weight);
    }

    public function getBackspacing(): ?CarModelWheelBackspacing
    {
        return false === empty($this->backspacing) ? new CarModelWheelBackspacing($this->backspacing) : null;
    }

    public function getPetrolId(): CarModelPetrolUid
    {
        return new CarModelPetrolUid($this->petrol_id);
    }

    public function getPetrolName(): CarModelPetrolName
    {
        return new CarModelPetrolName($this->petrol_name);
    }

    public function getPetrolHp(): CarModelPetrolHP
    {
        return new CarModelPetrolHP($this->petrol_hp);
    }
}