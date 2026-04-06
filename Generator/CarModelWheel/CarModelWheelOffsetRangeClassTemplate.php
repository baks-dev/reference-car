<?php

namespace BaksDev\Reference\Car\Generator\CarModelWheel;

class CarModelWheelOffsetRangeClassTemplate
{
    public static function getTemplate($data): string
    {
        $template = file_get_contents(__DIR__.'/CarModelWheelOffsetRangeClassTemplate.php.tpl');
        $carModelWheelOffsetRangeFullNamespace =
            'BaksDev\Reference\Car\Type\CarModelWheels\OffsetRange\ModelWheels\Collection\\'.
            $data->getGeneration()['model']['class_name'];

        $carModelWheelNamespace = 'BaksDev\Reference\Car\Type\CarModelWheels\Id\ModelWheels\Collection\\'.
            $data->getGeneration()['model']['class_name'].'\\'.$data->getClassName();

        return str_replace(
            ['{{classNamespace}}', '{{className}}', '{{value}}', '{{carModelWheelNamespace}}'],
            [$carModelWheelOffsetRangeFullNamespace, $data->getClassName(), $data->getOffsetRange(), $carModelWheelNamespace],
            $template,
        );
    }
}
