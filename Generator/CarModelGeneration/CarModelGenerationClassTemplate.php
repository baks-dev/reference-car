<?php

namespace BaksDev\Reference\Car\Generator\CarModelGeneration;


use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;

class CarModelGenerationClassTemplate
{
    public static function getTemplate($data): string
    {
        $uid = new CarModelGenerationUid();
        $generation = $data->getAll();
        $className = $generation['class_name'];
        $modelNamespace = 'BaksDev\\Reference\\Car\\Type\\CarModels\\Id\\Models\\Collection\\'.$generation['model']['class_name'];
        $title = preg_replace('/\s+| /u', '', trim($generation['title']));

        $template = file_get_contents(__DIR__.'/CarModelGenerationClassTemplate.php.tpl');

        return str_replace(
            ['{{className}}', '{{modelNamespace}}', '{{uid}}', '{{title}}'],
            [$className, $modelNamespace, (string) $uid, $title],
            $template,
        );
    }
}
