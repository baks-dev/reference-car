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

namespace BaksDev\Reference\Car\Controller\Public\CarModelPetrol;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Reference\Car\Forms\CarBrandFilter\CarFilterForm;
use BaksDev\Reference\Car\Repository\CarModelPetrolByNameAndHP\CarModelPetrolByNameAndHPInterface;
use BaksDev\Reference\Car\Repository\CarModelWheelsByModelPetrolNameAndHP\CarModelWheelsByModelPetrolNameAndHPInterface;
use BaksDev\Reference\Car\Type\CarModelPetrols\HP\CarModelPetrolHP;
use BaksDev\Reference\Car\Type\CarModelPetrols\Name\CarModelPetrolName;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class ShowController extends AbstractController
{
    #[Route('/auto/{brandName}/{modelName}/{modelPetrolName}/{modelPetrolHP}', name: 'car-models-petrols.public.show', methods: ['GET'])]
    public function show(
        $brandName,
        $modelName,
        $modelPetrolName,
        $modelPetrolHP,
        CarModelPetrolByNameAndHPInterface $carModelPetrolByNameAndHP,
        CarModelWheelsByModelPetrolNameAndHPInterface $carModelWheelsByModelPetrolId,
        Request $request,
    ): Response
    {
        $form = $this->createForm(CarFilterForm::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
        }

        $modelPetrolName = str_replace('-', ' ', $modelPetrolName);

        // Ищем числа в начале строки
        if(preg_match('/^(\d+)/', $modelPetrolName, $matches))
        {
            $number = $matches[1];

            // Если число двузначное, вставляем точку после первой цифры
            if(strlen($number) >= 2)
            {
                $formattedNumber = substr($number, 0, 1).'.'.substr($number, 1);
                $modelPetrolName = preg_replace('/^\d+/', $formattedNumber, $modelPetrolName);
            }
        }

        $modelPetrolName = new CarModelPetrolName($modelPetrolName);
        $modelPetrolHP = new CarModelPetrolHP($modelPetrolHP);

        $carModelPetrol = $carModelPetrolByNameAndHP
            ->forModelPetrolName($modelPetrolName)
            ->forModelPetrolHP($modelPetrolHP)
            ->find();

        $carModelWheels = $carModelWheelsByModelPetrolId
            ->forModelPetrolName($modelPetrolName)
            ->forModelPetrolHP($modelPetrolHP)
            ->findAll();

        return $this->render([
            'carModelPetrol' => $carModelPetrol,
            'carModelWheels' => $carModelWheels,
            'form' => $form->createView(),
        ]);
    }
}