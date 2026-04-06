<?php

namespace BaksDev\Reference\Car\Generator\CarModelGeneration;

class CarModelGenerationNameClassTemplate
{
    public static function getTemplate($data): string
    {
        $generation = $data->getAll();
        $className = $generation['class_name'];
        $title = preg_replace('/\s+| /u', '', trim($generation['title']));

        $template = file_get_contents(__DIR__.'/CarModelGenerationNameClassTemplate.php.tpl');

        return str_replace(
            ['{{className}}', '{{title}}'],
            [$className, $title],
            $template,
        );
    }
}
