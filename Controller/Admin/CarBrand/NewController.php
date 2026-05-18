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

namespace BaksDev\Reference\Car\Controller\Admin\CarBrand;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Reference\Car\Entity\CarBrand\CarBrand;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarBrand\CarBrandDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarBrand\CarBrandForm;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarBrand\CarBrandHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
#[RoleSecurity('ROLE_REFERENCE_CAR_NEW')]
final class NewController extends AbstractController
{
    #[Route('/admin/car-brands/new', name: 'admin.car-brands.newedit.new', methods: ['GET', 'POST'])]
    public function news(Request $request, CarBrandHandler $CarBrandHandler): Response
    {
        $CarBrandDTO = new CarBrandDTO();


        // Форма
        $form = $this
            ->createForm
            (
                CarBrandForm::class,
                $CarBrandDTO,
                ['action' => $this->generateUrl('reference-car:admin.car-brands.newedit.new')]
            )
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('car_brand_newedit'))
        {
//            if(false === empty($CarBrandDTO->getImage()))
//            {
//                $CarBrandImageDTO = $CarBrandDTO->getImage();
//                $file = $CarBrandImageDTO->getFile();
//
//                $CarBrandImageDTO
//                    ->setSize($file->getSize())
//                    ->setExt($file->getExtension())
//                    ->setName($file);
//            }

            $handle = $CarBrandHandler->handle($CarBrandDTO);

            $this->addFlash
            (
                'page.brand.new',
                $handle instanceof CarBrand ? 'success.new' : 'danger.new',
                'reference-car.admin',
                $handle
            );

            return $handle instanceof CarBrand ?
                $this->redirectToRoute('reference-car:admin.car-brands.index') :
                $this->redirectToReferer();
        }


        return $this->render(['form' => $form->createView()]);
    }
}