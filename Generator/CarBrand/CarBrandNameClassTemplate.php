<?php

namespace BaksDev\Reference\Car\Generator\CarBrand;

use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;

class CarBrandNameClassTemplate
{
    public static function getTemplate($data): string
    {
        $uid = new CarBrandUid();
        $brand = $data->getAll();
        $template = file_get_contents(__DIR__.'/CarBrandNameClassTemplate.php.tpl');

        $replacements = [
            '{{className}}' => $brand['class_name'],
            '{{brandNamespace}}' => $data->getNamespace().$data->getClassName(),
            '{{brandTitle}}' => $brand['title'],
            '{{uid}}' => (string) $uid,
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template,
        );
    }
}
