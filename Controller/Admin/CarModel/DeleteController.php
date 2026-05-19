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

namespace BaksDev\Reference\Car\Controller\Admin\CarModel;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\UseCase\Admin\Delete\CarModel\CarModelDeleteDTO;
use BaksDev\Reference\Car\UseCase\Admin\Delete\CarModel\CarModelDeleteForm;
use BaksDev\Reference\Car\UseCase\Admin\Delete\CarModel\CarModelDeleteHandler;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
#[RoleSecurity('ROLE_REFERENCE_CAR_DELETE')]
final class DeleteController extends AbstractController
{
    #[Route('/admin/car-models/delete/{model}', name: 'admin.car-models.delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        #[MapEntity] CarModel $model,
        CarModelDeleteHandler $DeleteHandler,
    ): Response
    {
        $CarModelDeleteDTO = new CarModelDeleteDTO();
        $model->getDto($CarModelDeleteDTO);

        $form = $this
            ->createForm(
                CarModelDeleteForm::class,
                $CarModelDeleteDTO,
                ['action' => $this->generateUrl(
                    'reference-car:admin.car-models.delete',
                    ['model' => $CarModelDeleteDTO->getId()]
                )]
            )
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('car_model_delete'))
        {
            $handle = $DeleteHandler->handle($CarModelDeleteDTO);

            $this->addFlash
            (
                'page.model.delete',
                $handle instanceof CarModel ? 'success.delete' : 'danger.delete',
                'reference-car.admin',
                $handle
            );

            return $handle instanceof CarModel ?
                $this->redirectToRoute('reference-car:admin.car-models.index') : $this->redirectToReferer();
        }

        return $this->render(['form' => $form->createView(), 'name' => $model->getName()]);
    }
}