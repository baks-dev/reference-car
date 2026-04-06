<?php

namespace BaksDev\Reference\Car\Generator\CarModel;

use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;

class CarModelClassTemplate
{
    public static function getTemplate($data): string
    {
        $uid = new CarModelUid();
        $model = $data->getAll();
        $className = $model['class_name'];
        $brandNamespace = 'BaksDev\\Reference\\Car\\Type\\CarBrands\\Id\\Brands\\Collection\\'.$model['brand']['class_name'];

        $template = file_get_contents(__DIR__.'/CarModelClassTemplate.php.tpl');

        return str_replace(
            ['{{className}}', '{{brandNamespace}}', '{{uid}}'],
            [$className, $brandNamespace, (string) $uid],
            $template,
        );
    }
}
