<?php

namespace BaksDev\Reference\Car\Generator\CarModel;

class CarModelNameClassTemplate
{
    public static function getTemplate($data): string
    {
        $model = $data->getAll();
        $className = $model['class_name'];

        $template = file_get_contents(__DIR__.'/CarModelNameClassTemplate.php.tpl');

        return str_replace(
            ['{{className}}', '{{modelTitle}}'],
            [$className, $model['title']],
            $template,
        );
    }
}
