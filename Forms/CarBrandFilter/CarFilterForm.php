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

namespace BaksDev\Reference\Car\Forms\CarBrandFilter;

use BaksDev\Reference\Car\Repository\AllCarBrands\AllCarBrandsInterface;
use BaksDev\Reference\Car\Repository\AllCarBrands\AllCarBrandsResult;
use BaksDev\Reference\Car\Repository\CarModelGenerationsByModel\CarModelGenerationsByModelInterface;
use BaksDev\Reference\Car\Repository\CarModelGenerationsByModelUrl\CarModelGenerationsByModelUrlInterface;
use BaksDev\Reference\Car\Repository\CarModelPetrolsByGeneration\CarModelPetrolsByGenerationIdInterface;
use BaksDev\Reference\Car\Repository\CarModelPetrolsByGeneration\CarModelPetrolsByGenerationIdResult;
use BaksDev\Reference\Car\Repository\CarModelPetrolsByGenerationUrl\CarModelPetrolsByGenerationUrlInterface;
use BaksDev\Reference\Car\Repository\CarModelsByBrand\CarModelsByBrandInterface;
use BaksDev\Reference\Car\Repository\CarModelsByBrand\CarModelsByBrandResult;
use BaksDev\Reference\Car\Repository\CarModelsByBrandUrl\CarModelsByBrandUrlInterface;
use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;
use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CarFilterForm extends AbstractType
{
    public function __construct(
        private readonly AllCarBrandsInterface $AllCarBrandsRepository,
        private readonly CarModelsByBrandUrlInterface $CarModelsByBrandUrlRepository,
        private readonly CarModelGenerationsByModelUrlInterface $CarModelGenerationsByModelUrlRepository,
        private readonly CarModelPetrolsByGenerationUrlInterface $CarModelPetrolsByGenerationUrlRepository,
        private readonly UrlGeneratorInterface $UrlGenerator
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $brands = $this->AllCarBrandsRepository->findAll();
        $brandChoices = [];


        /** @var AllCarBrandsResult $brand */
        foreach($brands as $brand)
        {
            $brandChoices[(string)$brand->getName()] = (string)$brand->getUrl();
        }


        $builder
            ->add('brand', ChoiceType::class, [
                'label' => 'Марка:',
                'required' => true,
                'choices' => $brandChoices,
                'placeholder' => 'Выберите марку...',
                'attr' => ['class' => 'rounded-4 py-2 w-100 mb-0 form-select', 'id' => 'car_filter_form_brand'],
            ]);

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function(FormEvent $event) {
                $form = $event->getForm();

                $brand = $form->getData() != null ? $form->getData()['brand'] : null;

                $models = $brand ? $this->CarModelsByBrandUrlRepository->findAll($brand) : [];

                $modelsChoices = [];

                if($models != null)
                {
                    /** @var CarModelsByBrandResult $model */
                    foreach($models as $model)
                    {
                        $modelsChoices[(string)$model->getName()] = (string)$model->getUrl();
                    }
                }

                $form->add('model', ChoiceType::class, [
                    'label' => 'Модель:',
                    'required' => true,
                    'choices' => $modelsChoices,
                    'placeholder' => empty($modelsChoices) ? 'Сначала выберите марку' : 'Выберите модель...',
                    'disabled' => empty($modelsChoices),
                    'attr' => ['class' => 'rounded-4 py-2 w-100 mb-0 form-select', 'id' => 'car_filter_form_model'],
                ]);

                $model = $form->getData() != null ? $form->getData()['model'] : null;
                $modelGenerations = $model ? $this->CarModelGenerationsByModelUrlRepository->findAll($model) : [];

                $modelGenerationsChoices = [];

                if($modelGenerations != null)
                {
                    foreach($modelGenerations as $modelGeneration)
                    {
                        $name = $modelGeneration->getName()->getValue();
                        $modelGenerationsChoices[$name] = (string)$modelGeneration->getUrl();
                    }
                }

                $form->add('generation', ChoiceType::class, [
                    'label' => 'Поколение:',
                    'required' => true,
                    'choices' => $modelGenerationsChoices,
                    'placeholder' => empty($modelGenerationsChoices) ?
                        'Сначала выберите модель' : 'Выберите поколение...',
                    'disabled' => empty($modelGenerationsChoices),
                    'attr' => ['class' => 'rounded-4 py-2 w-100 mb-0 form-select', 'id' => 'car_filter_form_model'],
                ]);

                $generation = $form->getData() != null ?
                    $form->getData()['generation'] : null;
                $modelPetrols = $generation ? $this->CarModelPetrolsByGenerationUrlRepository->findAll($generation) : [];

                $modelPetrolsChoices = [];


                if($modelPetrols != null)
                {
                    /** @var CarModelPetrolsByGenerationIdResult $modelPetrol */
                    foreach($modelPetrols as $modelPetrol)
                    {
                        $modelPetrolsChoices[
                            $modelPetrol->getGenerationName()
                            .' '
                            .$modelPetrol->getName()
                        ] = (string)$modelPetrol->getUrl();
                    }
                }


                $form->add('petrol', ChoiceType::class, [
                    'label' => 'Модификация:',
                    'required' => true,
                    'choices' => $modelPetrolsChoices,
                    'placeholder' => empty($modelPetrolsChoices) ?
                        'Сначала выберите поколение' : 'Выберите модификацию...',
                    'disabled' => empty($modelPetrolsChoices),
                    'attr' => [
                        'class' => 'rounded-4 py-2 w-100 mb-0 form-select',
                        'id' => 'car_filter_form_petrol',
                    ],
                ]);
            },
        );

        $builder->add('_token', HiddenType::class, ['data' => '']); // CSRF токен будет добавлен в шаблоне

        $builder->add(
            'car_filter_form',
            SubmitType::class,
            ['label' => 'Submit', 'label_html' => true, 'attr' => ['class' => 'btn-light']],
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'car_filter',
            'attr' => [
                'class' => 'w-100',
                'id' => 'carFilterForm',
//                'action' => $this->UrlGenerator->generate('reference-car:public.car-models-petrols.show')
            ],
        ]);
    }
}