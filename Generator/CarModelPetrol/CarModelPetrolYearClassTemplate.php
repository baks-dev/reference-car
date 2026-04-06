<?php

namespace BaksDev\Reference\Car\Generator\CarModelPetrol;

class CarModelPetrolYearClassTemplate
{
    public static function getTemplate($data, $year): string
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

        $template = file_get_contents(__DIR__.'/CarModelPetrolYearClassTemplate.php.tpl');

        return str_replace(
            ['{{petrolClassName}}', '{{modelClassName}}', '{{year}}'],
            [$petrolClassName, $modelClassName, $year],
            $template,
        );
    }
}
