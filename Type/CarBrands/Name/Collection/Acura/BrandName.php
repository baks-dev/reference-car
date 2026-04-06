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

namespace BaksDev\Reference\Car\Type\CarBrands\Name\Collection\Acura;

use BaksDev\Reference\Car\Type\CarBrands\Id\Collection\Acura\Brand;
use BaksDev\Reference\Car\Type\CarBrands\Name\CarBrandName;
use BaksDev\Reference\Car\Type\CarBrands\Name\CarBrandsNameInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('baks.car.brands')]
final class BrandName implements CarBrandsNameInterface
{
    /** Uid (ID) бренда */
    public const string CAR_BRAND_UID = Brand::CAR_BRAND_UID;

    /** Значение названия бренда */
    public const string CAR_BRAND_VALUE = 'Acura';

    /** @var string[] Список для фильтрации */
    public const array HAYSTACK = ['Acura'];

    public static function getUid(): string
    {
        return self::CAR_BRAND_UID;
    }

    public static function getValue(): CarBrandName
    {
        return new CarBrandName(self::CAR_BRAND_VALUE);
    }

    public static function equals(mixed $value): bool
    {
        if(method_exists($value, '__toString'))
        {
            $value = (string) $value;
        }

        return array_any(self::HAYSTACK, static fn($item) => str_contains(mb_strtolower($value), mb_strtolower($item)));
    }

    public static function sort(): int
    {
        return 2;
    }

}
