<?php

namespace BaksDev\Reference\Car\Generator\CarModelWheel;

class CarModelWheelProfileClassTemplate
{
    public static function getTemplate($data, $value): string
    {
        $carModelWheel = $data->getAll();

        $template = file_get_contents(__DIR__.'/CarModelWheelProfileClassTemplate.php.tpl');
        $carModelWheelDiameterFullNamespace =
            'BaksDev\Reference\Car\Type\CarModelWheels\Profile\ModelWheels\Collection\\'.
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
