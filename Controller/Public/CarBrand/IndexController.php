<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Reference\Car\Controller\Public\CarBrand;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Reference\Car\Forms\CarBrandFilter\CarFilterForm;
use BaksDev\Reference\Car\Repository\AllCarBrands\AllCarBrandsInterface;
use BaksDev\Reference\Car\Repository\CarModelPetrolById\CarModelPetrolByIdInterface;
use BaksDev\Reference\Car\Repository\CarModelWheelsByModelPetrolId\CarModelWheelsByModelPetrolIdInterface;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class IndexController extends AbstractController
{
    #[Route('/auto', name: 'car-brands.public.index', methods: ['GET', 'POST'], priority: 999)]
    public function index(
        Request $request,
        AllCarBrandsInterface $allCarBrands,
        CarModelPetrolByIdInterface $carModelPetrolById,
        CarModelWheelsByModelPetrolIdInterface $carModelWheelsByModelPetrolId,
        int $page = 0,
    ): Response
    {
        $formData = [
            'brand' => $request->request->get('brand') ?? null,
            'model' => $request->request->get('model') ?? null,
            'model_petrol' => $request->request->get('model_petrol') ?? null,
        ];
        $form = $this->createForm(CarFilterForm::class, $formData);
        $form->handleRequest($request);

        if($request->isXmlHttpRequest())
        {
            return $this->render(
                [
                    'form' => $form->createView(),
                ],
                file: 'car-brand/car-filter-form/car-filter-form.html.twig',
            );
        }

        if($form->isSubmitted() && $form->isValid())
        {
            $data = $request->get('car_filter_form');

            $carModelPetrolUid = new CarModelPetrolUid($data['model_petrol']);

            $carModelPetrol = $carModelPetrolById
                ->forModelPetrol($carModelPetrolUid)
                ->find();

            $carModelWheels = $carModelWheelsByModelPetrolId
                ->forModelPetrol($carModelPetrolUid)
                ->findAll();

            return $this->render([
                'form' => $form->createView(),
                'carModelPetrol' => $carModelPetrol,
                'carModelWheels' => $carModelWheels,
            ],
                file: 'car-brand/petrol-detail/template.html.twig');
        }

        $query = $allCarBrands->findAll();

        return $this->render([
            'form' => $form->createView(),
            'query' => $query,
        ]);
    }
}
