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

namespace BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModel;

use BaksDev\Reference\Car\Entity\CarModel\CarModelInterface;
use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModel\CarModelName\CarModelNameDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class CarModelDTO implements CarModelInterface
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    private CarModelUid $id;

    #[Assert\Valid]
    private CarModelNameDTO $name;

    #[Assert\NotBlank]
    #[Assert\Uuid]
    private CarBrandUid $brand;

    public function __construct()
    {
        $this->name = new CarModelNameDTO();
    }

    public function getId(): CarModelUid
    {
        return $this->id;
    }

    public function setId(CarModelUid $id): void
    {
        $this->id = $id;
    }

    public function getName(): CarModelNameDTO
    {
        return $this->name;
    }

    public function getBrand(): CarBrandUid
    {
        return $this->brand;
    }

    public function setBrand(CarBrandUid $brand): void
    {
        $this->brand = $brand;
    }
}
