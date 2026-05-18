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

namespace BaksDev\Reference\Car\Entity\CarModelGeneration;

use BaksDev\Core\Entity\EntityState;
use BaksDev\Reference\Car\Entity\CarModelGeneration\Image\CarModelGenerationImage;
use BaksDev\Reference\Car\Entity\CarModelGeneration\Name\CarModelGenerationName;
use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;


/* CarModelGeneration */

#[ORM\Entity]
#[ORM\Table(name: 'car_model_generation')]
class CarModelGeneration extends EntityState
{
    /** ID */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: CarModelGenerationUid::TYPE)]
    private CarModelGenerationUid $id;


    /** Название поколения */
    #[ORM\OneToOne(targetEntity: CarModelGenerationName::class, mappedBy: 'generation', cascade: ['all'])]
    private CarModelGenerationName $name;


    /** Изображение */
    #[ORM\OneToOne(
        targetEntity: CarModelGenerationImage::class,
        mappedBy: 'generation',
        cascade: ['all'],
        fetch: 'EAGER'
    )]
    private ?CarModelGenerationImage $image = null;


    /** ID модели */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(name: 'model', type: CarModelUid::TYPE)]
    private CarModelUid $model;

    public function __construct(CarModelGenerationUid $id)
    {
        $this->id = $id;
        $this->model = new CarModelUid();
        $this->name = new CarModelGenerationName($this);
    }

    public function __clone()
    {
        $this->id = clone $this->id;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getId(): CarModelGenerationUid
    {
        return $this->id;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CarModelGenerationInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof CarModelGenerationInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function getName(): ?CarModelGenerationName
    {
        return $this->name;
    }

    public function getModel(): CarModelUid
    {
        return $this->model;
    }

    public function setModel(CarModelUid $model): void
    {
        $this->model = $model;
    }

    public function getUploadImage(): ?CarModelGenerationImage
    {
        return $this->image;
    }
}
