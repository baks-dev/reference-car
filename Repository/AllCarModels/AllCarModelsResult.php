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

namespace BaksDev\Reference\Car\Repository\AllCarModels;

use BaksDev\Reference\Car\Type\CarBrands\Name\CarBrandName;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use BaksDev\Reference\Car\Type\CarModels\Name\CarModelName;

final readonly class AllCarModelsResult
{
    public function __construct(
        private string $id,
        private string $name,
        private string $url,
        private string $brand_name,
        private string $brand_url
    ) {}

    public function getId(): CarModelUid
    {
        return new CarModelUid($this->id);
    }

    public function getName(): CarModelName
    {
        return new CarModelName($this->name);
    }

    public function getUrl(): string
    {
        return $this->url;
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
