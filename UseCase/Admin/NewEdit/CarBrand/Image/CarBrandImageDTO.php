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

namespace BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarBrand\Image;

use BaksDev\Reference\Car\Entity\CarBrand\Image\CarBrandImageInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;


/** @see CarBrandImage */
final class CarBrandImageDTO implements CarBrandImageInterface
{
    /** Обложка бренда */
    public ?File $file = null;


    /** Название файла */
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $name = null;


    /** Расширениe файла */
    #[Assert\NotBlank]
    #[Assert\Choice(['png', 'gif', 'jpg', 'jpeg', 'webp'])]
    private ?string $ext = null;


    /** Размер файла */
    #[Assert\NotBlank]
    #[Assert\Range(max: 10485760)] // 1024 * 1024 * 10
    private int $size = 0;


    /** Флаг загрузки файла CDN */
    private bool $cdn = false;


    /** Название файла */
    public function getName(): ?string
    {
        return $this->name;
    }


    /** Расширений файла */
    public function getExt(): ?string
    {
        return $this->ext;
    }


    /** Наличие изображения на CDN */
    public function getCdn(): bool
    {
        return $this->cdn;
    }


    public function setFile(?File $file): self
    {
        $this->file = $file;
        return $this;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setExt(?string $ext): self
    {
        $this->ext = $ext;
        return $this;
    }

    public function setCdn(bool $cdn): self
    {
        $this->cdn = $cdn;
        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;
        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }
}