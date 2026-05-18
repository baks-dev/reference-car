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

namespace BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelPetrol;

use BaksDev\Reference\Car\Entity\CarModelPetrol\CarModelPetrolInterface;
use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelPetrol\CarModelPetrolHP\CarModelPetrolHPDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelPetrol\CarModelPetrolKW\CarModelPetrolKWDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelPetrol\CarModelPetrolName\CarModelPetrolNameDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelPetrol\CarModelPetrolPS\CarModelPetrolPSDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelPetrol\CarModelPetrolYear\CarModelPetrolYearDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class CarModelPetrolDTO implements CarModelPetrolInterface
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    private CarModelPetrolUid $id;

    #[Assert\Valid]
    private CarModelPetrolNameDTO $name;

    #[Assert\Valid]
    private CarModelPetrolHPDTO $hp;

    #[Assert\Valid]
    private CarModelPetrolKWDTO $kw;

    #[Assert\Valid]
    private CarModelPetrolPSDTO $ps;

    #[Assert\Valid]
    private CarModelPetrolYearDTO $year;

    #[Assert\NotBlank]
    #[Assert\Uuid]
    private CarModelGenerationUid $generation;

    public function __construct()
    {
        $this->id = new CarModelPetrolUid();
        $this->name = new CarModelPetrolNameDTO();
        $this->hp = new CarModelPetrolHPDTO();
        $this->kw = new CarModelPetrolKWDTO();
        $this->ps = new CarModelPetrolPSDTO();
        $this->year = new CarModelPetrolYearDTO();
    }

    public function getId(): CarModelPetrolUid
    {
        return $this->id;
    }

    public function setId(CarModelPetrolUid $id): void
    {
        $this->id = $id;
    }

    public function getName(): CarModelPetrolNameDTO
    {
        return $this->name;
    }

    public function getHp(): CarModelPetrolHPDTO
    {
        return $this->hp;
    }

    public function getKw(): CarModelPetrolKWDTO
    {
        return $this->kw;
    }

    public function getPs(): CarModelPetrolPSDTO
    {
        return $this->ps;
    }

    public function getYear(): CarModelPetrolYearDTO
    {
        return $this->year;
    }

    public function setYear(CarModelPetrolYearDTO $year): self
    {
        $this->year = $year;
        return $this;
    }

    public function getGeneration(): CarModelGenerationUid
    {
        return $this->generation;
    }

    public function setGeneration(CarModelGenerationUid $generation): self
    {
        $this->generation = $generation;
        return $this;
    }
}
