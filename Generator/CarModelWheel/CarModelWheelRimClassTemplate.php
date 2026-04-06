<?php

namespace BaksDev\Reference\Car\Generator\CarModelWheel;

class CarModelWheelRimClassTemplate
{
    public static function getTemplate($data): string
    {
        $template = file_get_contents(__DIR__.'/CarModelWheelRimClassTemplate.php.tpl');
        $carModelWheelRimFullNamespace =
            'BaksDev\Reference\Car\Type\CarModelWheels\Rim\ModelWheels\Collection\\'.
            $data->getGeneration()['model']['class_name'];

        $carModelWheelNamespace = 'BaksDev\Reference\Car\Type\CarModelWheels\Id\ModelWheels\Collection\\'.
            $data->getGeneration()['model']['class_name'].'\\'.$data->getClassName();

        return str_replace(
            ['{{classNamespace}}', '{{className}}', '{{value}}', '{{carModelWheelNamespace}}'],
            [$carModelWheelRimFullNamespace, $data->getClassName(), $data->getRim(), $carModelWheelNamespace],
            $template,
        );
    }
}
