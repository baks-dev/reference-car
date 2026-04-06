<?php

namespace BaksDev\Reference\Car\Generator\CarModelPetrol;

use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;

class CarModelPetrolClassTemplate
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
        $carModelGenerationFullNamespace = implode('\\', [
            'BaksDev',
            'Reference',
            'Car',
            'Type',
            'CarModelGenerations',
            'Id',
            'ModelGenerations',
            'Collection',
            $petrol['generation']['class_name'],
        ]);

        $useCarModelGenerationFullNamespace = class_exists($carModelGenerationFullNamespace)
            ? 'use '.$carModelGenerationFullNamespace.' as CarModelGeneration;'
            : '';

        $carModelGenerationValue = class_exists($carModelGenerationFullNamespace)
            ? 'CarModelGeneration::CAR_GENERATION_UID'
            : "''";

        $template = file_get_contents(__DIR__.'/CarModelPetrolClassTemplate.php.tpl');

        return str_replace(
            [
                '{{useCarModelGenerationFullNamespace}}',
                '{{carModelGenerationValue}}',
                '{{petrolClassName}}',
                '{{modelClassName}}',
                '{{uid}}',
                '{{petrol}}',
            ],
            [
                $useCarModelGenerationFullNamespace,
                $carModelGenerationValue,
                $petrolClassName,
                $modelClassName,
                $uid,
                $petrol['equipment_name'],
            ],
            $template,
        );
    }
}
