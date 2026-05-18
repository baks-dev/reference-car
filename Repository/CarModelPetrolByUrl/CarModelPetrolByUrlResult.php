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

namespace BaksDev\Reference\Car\Repository\CarModelPetrolByUrl;

use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;
use BaksDev\Reference\Car\Type\CarBrands\Name\CarBrandName;
use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;
use BaksDev\Reference\Car\Type\CarModelGenerations\Name\CarModelGenerationName;
use BaksDev\Reference\Car\Type\CarModelPetrols\HP\CarModelPetrolHP;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModelPetrols\KW\CarModelPetrolKW;
use BaksDev\Reference\Car\Type\CarModelPetrols\Name\CarModelPetrolName;
use BaksDev\Reference\Car\Type\CarModelPetrols\PS\CarModelPetrolPS;
use BaksDev\Reference\Car\Type\CarModelPetrols\Year\CarModelPetrolYear;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use BaksDev\Reference\Car\Type\CarModels\Name\CarModelName;

final readonly class CarModelPetrolByUrlResult
{
    public function __construct(
        private string $id,
        private string $name,
        private ?string $hp_value,
        private ?string $kw_value,
        private ?string $ps_value,
        private string $years,
        private string $generation_id,
        private string $generation_name,
        private string $generation_url,
        private ?string $generation_image_name,
        private ?string $generation_image_ext,
        private ?bool $generation_image_cdn,
        private string $model_id,
        private string $model_name,
        private string $model_url,
        private string $brand_id,
        private string $brand_name,
        private string $brand_url,
    ) {}

    public function getId(): CarModelPetrolUid
    {
        return new CarModelPetrolUid($this->id);
    }

    public function getHPValue(): ?CarModelPetrolHP
    {
        return false === empty($this->hp_value) ? new CarModelPetrolHP($this->hp_value) : null;
    }

    public function getKWValue(): ?CarModelPetrolKW
    {
        return false === empty($this->kw_value) ? new CarModelPetrolKW($this->kw_value) : null;
    }

    public function getPSValue(): ?CarModelPetrolPS
    {
        return false === empty($this->ps_value) ? new CarModelPetrolPS($this->ps_value) : null;
    }

    public function getName(): CarModelPetrolName
    {
        return new CarModelPetrolName($this->name);
    }

    public function getYears(): CarModelPetrolYear
    {
        return new CarModelPetrolYear($this->years);
    }

    public function getGenerationId(): CarModelGenerationUid
    {
        return new CarModelGenerationUid($this->generation_id);
    }

    public function getGenerationName(): CarModelGenerationName
    {
        return new CarModelGenerationName($this->generation_name);
    }

    public function getGenerationUrl(): string
    {
        return $this->generation_url;
    }

    public function getGenerationImageName(): ?string
    {
        return $this->generation_image_name;
    }

    public function getGenerationImageExt(): ?string
    {
        return $this->generation_image_ext;
    }

    public function getGenerationImageCdn(): bool
    {
        return true === $this->generation_image_cdn;
    }
    
    public function getModelId(): CarModelUid
    {
        return new CarModelUid($this->model_id);
    }

    public function getModelName(): CarModelName
    {
        return new CarModelName($this->model_name);
    }

    public function getModelUrl(): string
    {
        return $this->model_url;
    }

    public function getBrandId(): CarBrandUid
    {
        return new CarBrandUid($this->brand_id);
    }

    public function getBrandName(): CarBrandName
    {
        return new CarBrandName($this->brand_name);
    }

    public function getBrandUrl(): string
    {
        return $this->brand_url;
    }
}