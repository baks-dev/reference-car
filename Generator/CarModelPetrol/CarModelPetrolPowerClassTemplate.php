<?php

namespace BaksDev\Reference\Car\Generator\CarModelPetrol;

class CarModelPetrolPowerClassTemplate
{
    public static function getTemplate($data, $powerParam, $powerParamClassName): string
    {
        $petrol = $data->getAll();
        $enginePowerClassName = strtoupper(
            str_replace(' ', '', (
            explode('|', $data->getAll()['power'])[0]
            ),
            ),
        );
        $petrolClassName = 'Petrol'.$petrol['class_name'].$enginePowerClassName;
        $petrolPowerClassName = 'Petrol'.$petrol['class_name'].$powerParamClassName;
        $modelClassName = $petrol['generation']['model']['class_name'];
        $powerParamMeasure = explode(' ', $powerParam)[1];
        $powerParam = explode(' ', $powerParam)[0];

        $upperCaseMeasure = strtoupper($powerParamMeasure);
        $lowerCaseMeasure = strtolower($powerParamMeasure);


        $template = file_get_contents(__DIR__.'/CarModelPetrolPowerClassTemplate.php.tpl');

        return str_replace(
            [
                '{{petrolClassName}}',
                '{{petrolPowerClassName}}',
                '{{powerParamClassName}}',
                '{{powerParam}}',
                '{{powerParamMeasure}}',
                '{{modelClassName}}',
                '{{upperCaseMeasure}}',
                '{{lowerCaseMeasure}}',
            ],
            [
                $petrolClassName,
                $petrolPowerClassName,
                $powerParamClassName,
                $powerParam,
                $powerParamMeasure,
                $modelClassName,
                $upperCaseMeasure,
                $lowerCaseMeasure,
            ],
            $template,
        );
    }
}
