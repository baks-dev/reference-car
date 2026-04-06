<?php

namespace BaksDev\Reference\Car\Generator\CarModelWheel;

class CarModelWheelBackspacingClassTemplate
{
    public static function getTemplate($data): string
    {
        $template = file_get_contents(__DIR__.'/CarModelWheelBackspacingClassTemplate.php.tpl');
        $carModelWheelBackspacingFullNamespace =
            'BaksDev\Reference\Car\Type\CarModelWheels\Backspacing\ModelWheels\Collection\\'.
            $data->getGeneration()['model']['class_name'];

        $carModelWheelNamespace = 'BaksDev\Reference\Car\Type\CarModelWheels\Id\ModelWheels\Collection\\'.
            $data->getGeneration()['model']['class_name'].'\\'.$data->getClassName();

        return str_replace(
            ['{{classNamespace}}', '{{value}}', '{{className}}', '{{carModelWheelNamespace}}'],
            [$carModelWheelBackspacingFullNamespace, $data->getBackspacing(), $data->getClassName(), $carModelWheelNamespace],
            $template,
        );
    }
}
