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

namespace BaksDev\Reference\Car\UseCase\Admin\Delete\CarModelGeneration;

use BaksDev\Core\Entity\AbstractHandler;
use BaksDev\Reference\Car\Entity\CarModelGeneration\CarModelGeneration;

final class CarModelGenerationDeleteHandler extends AbstractHandler
{
    public function handle(CarModelGenerationDeleteDTO $command): string|CarModelGeneration|null
	{
        /** Валидация DTO */
        $this->setCommand($command);


        /** @var CarModelGeneration|null $CarModelGeneration */
        $CarModelGeneration = $this
            ->getRepository(CarModelGeneration::class)
            ->find($command->getId());

        if(false === ($CarModelGeneration instanceof CarModelGeneration))
        {
            return null;
        }


        /** Валидация всех объектов */
        if($this->validatorCollection->isInvalid())
        {
            return $this->validatorCollection->getErrorUniqid();
        }

        $this->remove($CarModelGeneration);

        $this->flush();


        /* Отправляем сообщение в шину */
        $this->messageDispatch->addClearCacheOther('reference-car');


        return $CarModelGeneration;
	}
}