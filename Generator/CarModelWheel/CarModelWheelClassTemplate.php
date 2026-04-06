<?php

namespace BaksDev\Reference\Car\Generator\CarModelWheel;

use BaksDev\Reference\Car\Type\CarModelWheels\Id\CarModelWheelUid;

class CarModelWheelClassTemplate
{
    public static function getTemplate($data): string
    {
        $uid = new CarModelWheelUid();
        $carModelWheel = $data->getAll();

        $modelPetrolNamespace = 'BaksDev\\Reference\\Car\\Type\\CarModelPetrols\\Id\\ModelPetrols\\Collection\\'.$carModelWheel['generation']['model']['class_name'].'\\'.$carModelWheel['relatedModelPetrolDirName'].'\\ModelPetrol';
        $carModelWheelFullNamespace =
            'BaksDev\Reference\Car\Type\CarModelWheels\Id\ModelWheels\Collection\\'.
            $data->getGeneration()['model']['class_name'];

        $template = file_get_contents(__DIR__.'/CarModelWheelClassTemplate.php.tpl');

        return str_replace(
            ['{{classNamespace}}', '{{className}}', '{{modelPetrolNamespace}}', '{{uid}}'],
            [$carModelWheelFullNamespace, $data->getClassName(), $modelPetrolNamespace, (string) $uid],
            $template,
        );
    }
}
