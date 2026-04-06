<?php
/*
 *  Copyright 2024.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Reference\Car\Messenger\WheelSize\CarEquipment;

use BaksDev\Reference\Car\Type\CarEquipments\CarEquipmentsInterface;

final class ParserCarEquipmentMessage
{
    /** Url комплектации */
    private string $url;

    /** Имя класса комплектации */
    private string $className;

    /** Имя комплектации */
    private string $equipmentName;

    /** Года выпуска */
    private string $production;

    /** Регионы продажи */
    private string $salesRegions;

    /** Мощность */
    private string $power;

    /** Двигатель */
    private string $engine;

    /** Центральное отверстие */
    private string $centerBore;

    /** Расположение болтов */
    private string $pcd;

    /** Колесные крепежи */
    private string $wheelFasteners;

    /** Размер резьбы */
    private string $threadSize;

    /** Момент затяжки колес */
    private string $wheelTighteningTorque;

    /** Имя комплектации */
    private string $title;

    /** Поколение комплектации */
    private array $generation;


    public function __construct(
        string $url,
        string $className,
        string $equipmentName,
        string $production,
        string $salesRegions,
        string $power,
        string $engine,
        string $centerBore,
        string $pcd,
        string $wheelFasteners,
        string $threadSize,
        string $wheelTighteningTorque,
        string $title,
        array $generation
    )
    {
        $this->url = (string) $url;
        $this->className = $className;
        $this->equipmentName = $equipmentName;
        $this->production = $production;
        $this->salesRegions = $salesRegions;
        $this->power = $power;
        $this->engine = $engine;
        $this->centerBore = $centerBore;
        $this->pcd = $pcd;
        $this->wheelFasteners = $wheelFasteners;
        $this->threadSize = $threadSize;
        $this->wheelTighteningTorque = $wheelTighteningTorque;
        $this->title = $title;
        $this->generation = $generation;
    }

    /** Url комплектации */
    public function getUrl(): string
    {
        return (string) $this->url;
    }

    public function getEquipmentName(): string
    {
        return $this->equipmentName;
    }

    public function getProduction(): string
    {
        return $this->production;
    }

    public function getSalesRegions(): string
    {
        return $this->salesRegions;
    }

    public function getPower(): string
    {
        return $this->power;
    }

    public function getEngine(): string
    {
        return $this->engine;
    }

    public function getCenterBore(): string
    {
        return $this->centerBore;
    }

    public function getPcd(): string
    {
        return $this->pcd;
    }

    public function getWheelFasteners(): string
    {
        return $this->wheelFasteners;
    }

    public function getThreadSize(): string
    {
        return $this->threadSize;
    }

    public function getWheelTighteningTorque(): string
    {
        return $this->wheelTighteningTorque;
    }

    public function getGeneration(): array
    {
        return $this->generation;
    }

    /** Namespace класса комплектации */
    public function getNamespace(): string
    {
        return CarEquipmentsInterface::EQUIPMENT_NAMESPACE.'Collection\\'.$this->className;
    }

    /** Имя класса комплектации */
    public function getClassName(): string
    {
        return $this->className;
    }

    /** Имя комплектации */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Возвращает все поля сообщения в виде массива
     *
     * @return array{
     *     url: string,
     *     class_name: string,
     *     equipment_name: string,
     *     production: string,
     *     sales_regions: string,
     *     power: string,
     *     engine: string,
     *     center_bore: string,
     *     pcd: string,
     *     wheel_fasteners: string,
     *     thread_size: string,
     *     wheel_tightening_torque: string,
     *     title: string,
     *     generation: array
     * }
     */
    public function getAll(): array
    {
        return [
            'href' => $this->url,
            'class_name' => $this->className,
            'equipment_name' => $this->equipmentName,
            'production' => $this->production,
            'sales_regions' => $this->salesRegions,
            'power' => $this->power,
            'engine' => $this->engine,
            'center_bore' => $this->centerBore,
            'pcd' => $this->pcd,
            'wheel_fasteners' => $this->wheelFasteners,
            'thread_size' => $this->threadSize,
            'wheel_tightening_torque' => $this->wheelTighteningTorque,
            'title' => $this->title,
            'generation' => $this->generation,
        ];
    }
}
