<?php
/*
 * Copyright 2026.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Reference\Car\Generator\CarModelGeneration;

use BaksDev\Reference\Car\Service\CarModelGeneration\CarModelGenerationClassCheckerDTO;
use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;

final class CarModelGenerationClassTemplate
{
    public static function getTemplate(CarModelGenerationClassCheckerDTO $data): string
    {
        $uid = new CarModelGenerationUid();
        $modelNamespace = $data->getModel()->getNamespace().$data->getModel()->getClassName();

        $template = file_get_contents(__DIR__.'/CarModelGenerationClassTemplate.php.tpl');

        $namespace = str_ends_with($data->getNamespace(), '\\') ? substr($data->getNamespace(), 0, -1) : $data->getNamespace();

        return str_replace(
            ['{{className}}', '{{namespace}}', '{{modelNamespace}}', '{{uid}}', '{{title}}'],
            [$data->getClassName(), $namespace, $modelNamespace, (string) $uid, $data->getTitle()],
            $template,
        );
    }
}
