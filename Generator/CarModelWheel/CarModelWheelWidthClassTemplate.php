<?php

namespace BaksDev\Reference\Car\Generator\CarModelWheel;

class CarModelWheelWidthClassTemplate
{
    public static function getTemplate($data, $value): string
    {
        $carModelWheel = $data->getAll();

        $template = file_get_contents(__DIR__.'/CarModelWheelWidthClassTemplate.php.tpl');
        $carModelWheelDiameterFullNamespace =
            'BaksDev\Reference\Car\Type\CarModelWheels\Width\ModelWheels\Collection\\'.
            $data->getGeneration()['model']['class_name'];

        $carModelWheelNamespace = 'BaksDev\Reference\Car\Type\CarModelWheels\Id\ModelWheels\Collection\\'.
            $data->getGeneration()['model']['class_name'].'\\'.$data->getClassName();

        return str_replace(
            ['{{classNamespace}}', '{{className}}', '{{value}}', '{{carModelWheelNamespace}}'],
            [$carModelWheelDiameterFullNamespace, $data->getClassName(), $value, $carModelWheelNamespace],
            $template,
        );
    }
}
