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

namespace BaksDev\Reference\Car\Entity\CarModelWheel;

use BaksDev\Core\Entity\EntityState;
use BaksDev\Reference\Car\Entity\CarModelWheel\Backspacing\CarModelWheelBackspacing;
use BaksDev\Reference\Car\Entity\CarModelWheel\Bar\CarModelWheelBar;
use BaksDev\Reference\Car\Entity\CarModelWheel\Diameter\CarModelWheelDiameter;
use BaksDev\Reference\Car\Entity\CarModelWheel\OffsetRange\CarModelWheelOffsetRange;
use BaksDev\Reference\Car\Entity\CarModelWheel\Profile\CarModelWheelProfile;
use BaksDev\Reference\Car\Entity\CarModelWheel\Rim\CarModelWheelRim;
use BaksDev\Reference\Car\Entity\CarModelWheel\TireWeight\CarModelWheelTireWeight;
use BaksDev\Reference\Car\Entity\CarModelWheel\Width\CarModelWheelWidth;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModelWheels\Id\CarModelWheelUid;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* CarModelWheel */

#[ORM\Entity]
#[ORM\Table(name: 'car_model_wheel')]
class CarModelWheel extends EntityState
{
    /** ID */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: CarModelWheelUid::TYPE)]
    private CarModelWheelUid $id;

    #[ORM\OneToOne(targetEntity: CarModelWheelDiameter::class, mappedBy: 'wheel', cascade: ['all'])]
    private CarModelWheelDiameter $diameter;

    #[ORM\OneToOne(targetEntity: CarModelWheelProfile::class, mappedBy: 'wheel', cascade: ['all'])]
    private CarModelWheelProfile $profile;

    #[ORM\OneToOne(targetEntity: CarModelWheelWidth::class, mappedBy: 'wheel', cascade: ['all'])]
    private CarModelWheelWidth $width;

    #[ORM\OneToOne(targetEntity: CarModelWheelBackspacing::class, mappedBy: 'wheel', cascade: ['all'])]
    private CarModelWheelBackspacing $backspacing;

    #[ORM\OneToOne(targetEntity: CarModelWheelBar::class, mappedBy: 'wheel', cascade: ['all'])]
    private CarModelWheelBar $bar;

    #[ORM\OneToOne(targetEntity: CarModelWheelOffsetRange::class, mappedBy: 'wheel', cascade: ['all'])]
    private CarModelWheelOffsetRange $range;

    #[ORM\OneToOne(targetEntity: CarModelWheelRim::class, mappedBy: 'wheel', cascade: ['all'])]
    private CarModelWheelRim $rim;

    #[ORM\OneToOne(targetEntity: CarModelWheelTireWeight::class, mappedBy: 'wheel', cascade: ['all'])]
    private CarModelWheelTireWeight $weight;

    /** ID model petrol */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(name: 'petrol', type: CarModelPetrolUid::TYPE)]
    private CarModelPetrolUid $petrol;

    public function __construct(CarModelWheelUid $id)
    {
        $this->id = $id;
        $this->petrol = new CarModelPetrolUid();
        $this->diameter = new CarModelWheelDiameter($this);
        $this->profile = new CarModelWheelProfile($this);
        $this->width = new CarModelWheelWidth($this);
        $this->backspacing = new CarModelWheelBackspacing($this);
        $this->bar = new CarModelWheelBar($this);
        $this->range = new CarModelWheelOffsetRange($this);
        $this->rim = new CarModelWheelRim($this);
        $this->weight = new CarModelWheelTireWeight($this);
    }

    public function __clone()
    {
        $this->id = clone $this->id;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getId(): CarModelWheelUid
    {
        return $this->id;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CarModelWheelInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof CarModelWheelInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function getDiameter(): CarModelWheelDiameter
    {
        return $this->diameter;
    }

    public function setDiameter(CarModelWheelDiameter $diameter): void
    {
        $this->diameter = $diameter;
    }

    public function getProfile(): CarModelWheelProfile
    {
        return $this->profile;
    }

    public function setProfile(CarModelWheelProfile $profile): void
    {
        $this->profile = $profile;
    }

    public function getWidth(): CarModelWheelWidth
    {
        return $this->width;
    }

    public function setWidth(CarModelWheelWidth $width): void
    {
        $this->width = $width;
    }

    public function setBackspacing(CarModelWheelBackspacing $backspacing): void
    {
        $this->backspacing = $backspacing;
    }

    public function setBar(CarModelWheelBar $bar): void
    {
        $this->bar = $bar;
    }

    public function setRange(CarModelWheelOffsetRange $range): void
    {
        $this->range = $range;
    }

    public function setRim(CarModelWheelRim $rim): void
    {
        $this->rim = $rim;
    }

    public function setWeight(CarModelWheelTireWeight $weight): void
    {
        $this->weight = $weight;
    }

    public function getPetrol(): CarModelPetrolUid
    {
        return $this->petrol;
    }

    public function setPetrol(CarModelPetrolUid $petrol): void
    {
        $this->petrol = $petrol;
    }
}
