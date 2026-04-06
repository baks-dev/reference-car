<?php

namespace BaksDev\Reference\Car\Generator\CarBrand;


use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;

class CarBrandClassTemplate
{
    public static function getTemplate($data): string
    {
        $uid = new CarBrandUid();
        $brand = $data->getAll();
        $className = $brand['class_name'];

        $template = file_get_contents(__DIR__.'/CarBrandClassTemplate.php.tpl');

        return str_replace(
            ['{{className}}', '{{uid}}'],
            [$className, (string) $uid],
            $template,
        );
    }
}
