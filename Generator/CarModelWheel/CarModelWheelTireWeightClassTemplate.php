<?php

namespace BaksDev\Reference\Car\Generator\CarModelWheel;

class CarModelWheelTireWeightClassTemplate
{
    public static function getTemplate($data): string
    {
        $template = file_get_contents(__DIR__.'/CarModelWheelTireWeightClassTemplate.php.tpl');
        $carModelWheelTireWeightFullNamespace =
            'BaksDev\Reference\Car\Type\CarModelWheels\TireWeight\ModelWheels\Collection\\'.
            $data->getGeneration()['model']['class_name'];

        $carModelWheelNamespace = 'BaksDev\Reference\Car\Type\CarModelWheels\Id\ModelWheels\Collection\\'.
            $data->getGeneration()['model']['class_name'].'\\'.$data->getClassName();

        return str_replace(
            ['{{classNamespace}}', '{{className}}', '{{value}}', '{{carModelWheelNamespace}}'],
            [$carModelWheelTireWeightFullNamespace, $data->getClassName(), $data->getTireWeight(), $carModelWheelNamespace],
            $template,
        );
    }
}
