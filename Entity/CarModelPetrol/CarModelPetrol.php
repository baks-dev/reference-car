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

namespace BaksDev\Reference\Car\Entity\CarModelPetrol;

use BaksDev\Core\Entity\EntityState;
use BaksDev\Reference\Car\Entity\CarModelPetrol\HP\CarModelPetrolHP;
use BaksDev\Reference\Car\Entity\CarModelPetrol\KW\CarModelPetrolKW;
use BaksDev\Reference\Car\Entity\CarModelPetrol\Name\CarModelPetrolName;
use BaksDev\Reference\Car\Entity\CarModelPetrol\PS\CarModelPetrolPS;
use BaksDev\Reference\Car\Entity\CarModelPetrol\SaleRegion\CarModelPetrolSaleRegion;
use BaksDev\Reference\Car\Entity\CarModelPetrol\Year\CarModelPetrolYear;
use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'car_model_petrol')]
class CarModelPetrol extends EntityState
{
    /** ID */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: CarModelPetrolUid::TYPE)]
    private CarModelPetrolUid $id;

    /** ID модели */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(name: 'model', type: CarModelUid::TYPE)]
    private CarModelUid $model;

    #[ORM\OneToOne(targetEntity: CarModelPetrolName::class, mappedBy: 'petrol', cascade: ['all'])]
    private CarModelPetrolName $name;

    #[ORM\OneToOne(targetEntity: CarModelPetrolPS::class, mappedBy: 'petrol', cascade: ['all'])]
    private CarModelPetrolPS $ps;

    #[ORM\OneToOne(targetEntity: CarModelPetrolKW::class, mappedBy: 'petrol', cascade: ['all'])]
    private CarModelPetrolKW $kw;

    #[ORM\OneToOne(targetEntity: CarModelPetrolHP::class, mappedBy: 'petrol', cascade: ['all'])]
    private CarModelPetrolHP $hp;

    /** ID поколения */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(name: 'generation', type: CarModelGenerationUid::TYPE, nullable: true)]
    private CarModelGenerationUid $generation;

    #[ORM\OneToMany(
        targetEntity: CarModelPetrolSaleRegion::class,
        mappedBy: 'petrol',
        cascade: ['all']
    )]
    private Collection $region;

    #[ORM\OneToMany(
        targetEntity: CarModelPetrolYear::class,
        mappedBy: 'petrol',
        cascade: ['all']
    )]
    private Collection $year;

    public function __construct(CarModelPetrolUid $id)
    {
        $this->id = $id;
        $this->model = new CarModelUid();
        $this->name = new CarModelPetrolName($this);
        $this->ps = new CarModelPetrolPS($this);
        $this->kw = new CarModelPetrolKW($this);
        $this->hp = new CarModelPetrolHP($this);
        $this->region = new ArrayCollection();
        $this->year = new ArrayCollection();
    }

    public function __clone()
    {
        $this->id = clone $this->id;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getId(): CarModelPetrolUid
    {
        return $this->id;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CarModelPetrolInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof CarModelPetrolInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function getName(): ?CarModelPetrolName
    {
        return $this->name;
    }

    public function getModel(): CarModelUid
    {
        return $this->model;
    }

    public function getPs(): CarModelPetrolPS
    {
        return $this->ps;
    }

    public function getKw(): CarModelPetrolKW
    {
        return $this->kw;
    }

    public function getHp(): CarModelPetrolHP
    {
        return $this->hp;
    }

    public function getRegion(): Collection
    {
        return $this->region;
    }

    public function getYear(): Collection
    {
        return $this->year;
    }

    public function setModel(CarModelUid $model): void
    {
        $this->model = $model;
    }
}
