<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarBrand;

use BaksDev\Reference\Car\Entity\CarBrand\CarBrandInterface;
use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarBrand\CarBrandName\CarBrandNameDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarBrand\Image\CarBrandImageDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class CarBrandDTO implements CarBrandInterface
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    private CarBrandUid $id;

    #[Assert\Valid]
    private CarBrandNameDTO $name;


    /** Обложка бренда */
    public ?CarBrandImageDTO $image = null;


    public function __construct()
    {
        $this->id = new CarBrandUid();
        $this->name = new CarBrandNameDTO();
        $this->image = new CarBrandImageDTO();
    }

    public function getId(): CarBrandUid
    {
        return $this->id;
    }

    public function setId(CarBrandUid $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): CarBrandNameDTO
    {
        return $this->name;
    }


    /** Обложка бренда */

    public function getImage(): ?CarBrandImageDTO
    {
        return $this->image;
    }

    public function setImage(?CarBrandImageDTO $image): self
    {
        $this->image = $image;
        return $this;
    }
}
