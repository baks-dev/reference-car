<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\AllCarModelWheels;

use BaksDev\Reference\Car\Type\CarModelPetrols\Name\CarModelPetrolName;
use BaksDev\Reference\Car\Type\CarModelWheels\Backspacing\CarModelWheelBackspacing;
use BaksDev\Reference\Car\Type\CarModelWheels\Bar\CarModelWheelBar;
use BaksDev\Reference\Car\Type\CarModelWheels\Diameter\CarModelWheelDiameter;
use BaksDev\Reference\Car\Type\CarModelWheels\Id\CarModelWheelUid;
use BaksDev\Reference\Car\Type\CarModelWheels\OffsetRange\CarModelWheelOffsetRange;
use BaksDev\Reference\Car\Type\CarModelWheels\Profile\CarModelWheelProfile;
use BaksDev\Reference\Car\Type\CarModelWheels\Rim\CarModelWheelRim;
use BaksDev\Reference\Car\Type\CarModelWheels\TireWeight\CarModelWheelTireWeight;
use BaksDev\Reference\Car\Type\CarModelWheels\Width\CarModelWheelWidth;

final class AllCarModelWheelsResult
{
    public function __construct(
        private readonly string $id,
        private readonly ?string $petrol_name,
        private readonly string $wheel_diameter,
        private readonly string $wheel_profile,
        private readonly string $wheel_width,
        private readonly string $wheel_rim,
        private readonly string $wheel_offset_range,
        private readonly string $wheel_bar,
        private readonly string $wheel_tire_weight,
        private readonly string $wheel_backspacing,
    ) {}

    public function getId(): CarModelWheelUid
    {
        return new CarModelWheelUid($this->id);
    }

    public function getPetrolName(): ?CarModelPetrolName
    {
        if($this->petrol_name === null)
        {
            return null;
        }

        return new CarModelPetrolName($this->petrol_name);
    }

    public function getWheelDiameter(): CarModelWheelDiameter
    {
        return new CarModelWheelDiameter($this->wheel_diameter);
    }

    public function getWheelProfile(): CarModelWheelProfile
    {
        return new CarModelWheelProfile($this->wheel_profile);
    }

    public function getWheelWidth(): CarModelWheelWidth
    {
        return new CarModelWheelWidth($this->wheel_width);
    }

    public function getWheelRim(): CarModelWheelRim
    {
        return new CarModelWheelRim($this->wheel_rim);
    }

    public function getWheelOffsetRange(): CarModelWheelOffsetRange
    {
        return new CarModelWheelOffsetRange($this->wheel_offset_range);
    }

    public function getWheelBar(): CarModelWheelBar
    {
        return new CarModelWheelBar($this->wheel_bar);
    }

    public function getWheelTireWeight(): CarModelWheelTireWeight
    {
        return new CarModelWheelTireWeight($this->wheel_tire_weight);
    }

    public function getWheelBackspacing(): CarModelWheelBackspacing
    {
        return new CarModelWheelBackspacing($this->wheel_backspacing);
    }
}
