<?php

namespace BaksDev\Reference\Car\Generator\CarModelPetrol;

use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;

class CarModelPetrolNameClassTemplate
{
    public static function getTemplate($data): string
    {

        $uid = new CarModelPetrolUid();
        $petrol = $data->getAll();
        $enginePowerClassName = strtoupper(
            str_replace(' ', '', (
            explode('|', $data->getAll()['power'])[0]
            ),
            ),
        );
        $petrolClassName = 'Petrol'.$petrol['class_name'].$enginePowerClassName;
        $modelClassName = $petrol['generation']['model']['class_name'];

        $template = file_get_contents(__DIR__.'/CarModelPetrolNameClassTemplate.php.tpl');

        return str_replace(
            ['{{petrolClassName}}', '{{modelClassName}}', '{{uid}}', '{{petrol}}'],
            [$petrolClassName, $modelClassName, $uid, $petrol['equipment_name']],
            $template,
        );
    }
}
