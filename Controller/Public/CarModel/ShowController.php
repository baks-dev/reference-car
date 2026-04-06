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

namespace BaksDev\Reference\Car\Controller\Public\CarModel;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Type\UidType\ParamConverter;
use BaksDev\Reference\Car\Forms\CarBrandFilter\CarFilterForm;
use BaksDev\Reference\Car\Repository\CarModelByName\CarModelByNameInterface;
use BaksDev\Reference\Car\Repository\CarModelPetrolsByModelName\CarModelPetrolsByModelNameInterface;
use BaksDev\Reference\Car\Type\CarModels\Name\CarModelName;
use BaksDev\Users\User\Type\Id\UserUid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class ShowController extends AbstractController
{
    #[Route('/auto/{brandName}/{modelName}', name: 'car-models.public.show', methods: ['GET'])]
    public function show(
        $brandName,
        #[ParamConverter(CarModelName::class, key: 'modelName')] $modelName = null,
        CarModelByNameInterface $carModelByName,
        CarModelPetrolsByModelNameInterface $carModelPetrolsByModelName,
        Request $request,
    ): Response
    {
        $modelName = new CarModelName($modelName);

        $form = $this->createForm(CarFilterForm::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
        }

        $carModel = $carModelByName
            ->forModelName($modelName)
            ->find();

        $carModelPetrols = $carModelPetrolsByModelName
            ->forModelName($modelName)
            ->findAll();

        return $this->render([
            'carModel' => $carModel,
            'carModelPetrols' => $carModelPetrols,
            'form' => $form->createView(),
        ]);
    }
}