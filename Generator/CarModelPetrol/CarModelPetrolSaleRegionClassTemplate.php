<?php

namespace BaksDev\Reference\Car\Generator\CarModelPetrol;

class CarModelPetrolSaleRegionClassTemplate
{
    public static function getTemplate($data, $saleRegion): string
    {
        $petrol = $data->getAll();
        $enginePowerClassName = strtoupper(
            str_replace(' ', '', (
            explode('|', $data->getAll()['power'])[0]
            ),
            ),
        );
        $petrolClassName = 'Petrol'.$petrol['class_name'].$enginePowerClassName;
        $modelClassName = $petrol['generation']['model']['class_name'];

        $template = file_get_contents(__DIR__.'/CarModelPetrolSaleRegionClassTemplate.php.tpl');

        return str_replace(
            ['{{petrolClassName}}', '{{modelClassName}}', '{{saleRegionClassName}}', '{{saleRegionValue}}'],
            [$petrolClassName, $modelClassName, $saleRegion['class_name'], $saleRegion['value']],
            $template,
        );
    }
}
