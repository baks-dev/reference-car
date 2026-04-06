<?php

namespace BaksDev\Reference\Car\Generator\CarModelWheel;

class CarModelWheelDiameterClassTemplate
{
    public static function getTemplate($data, $rimDiameter): string
    {
        $template = file_get_contents(__DIR__.'/CarModelWheelDiameterClassTemplate.php.tpl');
        $carModelWheelDiameterFullNamespace =
            'BaksDev\Reference\Car\Type\CarModelWheels\Diameter\ModelWheels\Collection\\'.
            $data->getGeneration()['model']['class_name'];

        $carModelWheelNamespace = 'BaksDev\Reference\Car\Type\CarModelWheels\Id\ModelWheels\Collection\\'.
            $data->getGeneration()['model']['class_name'].'\\'.$data->getClassName();

        return str_replace(
            ['{{classNamespace}}', '{{className}}', '{{value}}', '{{carModelWheelNamespace}}'],
            [$carModelWheelDiameterFullNamespace, $data->getClassName(), $rimDiameter, $carModelWheelNamespace],
            $template,
        );
    }
}
