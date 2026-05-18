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

namespace BaksDev\Reference\Car\Controller\Public\CarModelGeneration;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Type\Field\InputField;
use BaksDev\Field\Tire\Radius\Type\TireRadiusField;
use BaksDev\Products\Category\Repository\OneCategoryByFieldType\OneCategoryByFieldTypeInterface;
use BaksDev\Products\Product\Forms\ProductCategoryFilter\User\ProductCategoryFilterDTO;
use BaksDev\Products\Product\Forms\ProductCategoryFilter\User\ProductCategoryFilterForm;
use BaksDev\Reference\Car\Forms\CarBrandFilter\CarFilterForm;
use BaksDev\Reference\Car\Repository\CarModelGenerationByUrl\CarModelGenerationByUrlInterface;
use BaksDev\Reference\Car\Repository\CarModelPetrolsByGenerationUrl\CarModelPetrolsByGenerationUrlInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class ShowController extends AbstractController
{
    #[Route('/auto/{brandName}/{modelName}/{generationName}', name: 'public.car-generation.show', methods: ['GET', 'POST'])]
    public function show(
        string $brandName,
        string $modelName,
        string $generationName,
        OneCategoryByFieldTypeInterface $OneCategoryByFieldTypeRepository,
        CarModelGenerationByUrlInterface $CarModelGenerationByUrlRepository,
        CarModelPetrolsByGenerationUrlInterface $CarModelPetrolsByGenerationUrlRepository,
        Request $request,
    ): Response
    {
        /** Находим любую категорию шин (где типом торгового предложения будет являться радиус) */
        $categoryUid = $OneCategoryByFieldTypeRepository->find(new InputField(TireRadiusField::TYPE));


        /** Фильтр по параметрам */
        $productCategoryFilterDTO = new ProductCategoryFilterDTO($categoryUid);
        $productFilterForm = $this->createForm(ProductCategoryFilterForm::class,
            $productCategoryFilterDTO,
            ['action' => $this->generateUrl('products-product:public.catalog.index')]
        );


        $carGeneration = $CarModelGenerationByUrlRepository->find($generationName);

        $data = $request->get('car_filter_form');

        $formData = false === empty($data['brand']) ? [
            'brand' => $data['brand'] ?? null,
            'model' => $data['model'] ?? null,
            'generation' => $data['generation'] ?? null,
            'petrol' => $data['petrol'] ?? null,
        ] : [
            'brand' => $brandName,
            'model' => $modelName,
            'generation' => $generationName,
            'petrol' => null
        ];

        $form = $this
            ->createForm(CarFilterForm::class, $formData, ['action' => $this->generateUrl('reference-car:public.car-models-petrols.show', $formData)])
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            return $this->redirectToRoute('reference-car:public.car-models-petrols.show', $formData);
        }

        /* если форма отправлена AJAX - нам достаточно только формы поиска по типу автомобиля */
        if($request->isXmlHttpRequest())
        {
            return $this->render(
                ['form' => $form->createView()],
                dir: '/',
                file: 'public/car-filter-form/car-filter-form.html.twig',
            );
        }

        $carModelPetrols = $CarModelPetrolsByGenerationUrlRepository->findAll($generationName);

        return $this->render([
            'carGeneration' => $carGeneration,
            'carModelPetrols' => $carModelPetrols,
            'form' => $form->createView(),
            'filter_tire' => $productFilterForm->createView(),
        ]);
    }
}