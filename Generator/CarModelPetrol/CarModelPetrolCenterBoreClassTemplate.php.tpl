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

namespace BaksDev\Reference\Car\Type\CarModels\Collection\{{modelClassName}}\Petrol\{{petrolClassName}};

use BaksDev\Reference\Car\Type\CarModels\CarModelsCollection;
use BaksDev\Reference\Car\Type\CarModels\CarModelsInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use BaksDev\Reference\Car\Type\CarModels\ModelUid;
use BaksDev\Reference\Car\Type\CarModels\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModels\Collection\{{modelClassName}}\Model;

#[AutoconfigureTag('baks.car.generations')]
final class ModelPetrolCenterBore implements CarModelsInterface
{
/** Uid (ID) комплектации */
public const string CAR_MODEL_PETROL_UID = ModelPetrol::CAR_MODEL_PETROL_UID;

/** Значение Центрального отверстия */
public const string CAR_MODEL_PETROL_VALUE = '{{petrol}}';

/** @var string[] Список для фильтрации */
public const array HAYSTACK = ['{{petrol}}'];

/**
* Возвращает UID комплектации
*/
public function getUid(): CarModelPetrolUid
{
return self::CAR_MODEL_PETROL_UID;
}

/**
* Возвращает UID привязанной модели
*/
public function getModelUid(): ModelUid
{
return self::CAR_MODEL_UID;
}

/**
* Возвращает значение названия комплектации
*/
public function getValue(): string
{
return self::CAR_MODEL_PETROL_VALUE;
}

public static function equals(mixed $value): bool
{
if (is_object($value) && method_exists($value, '__toString')) {
$value = (string)$value;
}

if (is_string($value)) {
return array_any(
self::HAYSTACK,
static fn($item) => mb_strtolower($value) === mb_strtolower($item)
);
}

return false;
}

/**
* Метод фильтрует значение, удаляя его из строки
*/
public static function filter(string $model): string
{
return CarModelsCollection::filter(self::HAYSTACK, $model);
}

/**
* Сортировка (чем меньше число - тем первым в итерации будет значение)
*/
public static function sort(): int
{
return 2;
}
}
