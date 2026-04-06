<?php

namespace BaksDev\Reference\Car\Generator\CarModelWheel;

class CarModelWheelBarClassTemplate
{
    public static function getTemplate($data): string
    {
        $template = file_get_contents(__DIR__.'/CarModelWheelBarClassTemplate.php.tpl');
        $carModelWheelBarFullNamespace =
            'BaksDev\Reference\Car\Type\CarModelWheels\Bar\ModelWheels\Collection\\'.
            $data->getGeneration()['model']['class_name'];

        $carModelWheelNamespace = 'BaksDev\Reference\Car\Type\CarModelWheels\Id\ModelWheels\Collection\\'.
            $data->getGeneration()['model']['class_name'].'\\'.$data->getClassName();

        return str_replace(
            ['{{classNamespace}}', '{{className}}', '{{value}}', '{{carModelWheelNamespace}}'],
            [$carModelWheelBarFullNamespace, $data->getClassName(), $data->getBar(), $carModelWheelNamespace],
            $template,
        );
    }
}
