<?php

namespace BaksDev\Reference\Car\Forms\CarBrandFilter;

use BaksDev\Reference\Car\Repository\AllCarBrands\AllCarBrandsInterface;
use BaksDev\Reference\Car\Repository\CarModelPetrolsByModel\CarModelPetrolsByModelIdInterface;
use BaksDev\Reference\Car\Repository\CarModelsByBrand\CarModelsByBrandIdInterface;
use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarFilterForm extends AbstractType
{
    public function __construct(
        private readonly AllCarBrandsInterface $allCarBrands,
        private readonly CarModelsByBrandIdInterface $carModelsByBrand,
        private readonly CarModelPetrolsByModelIdInterface $carModelPetrolsByModel,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $brands = $this->allCarBrands->findAll();
        $brandChoices = [];

        foreach($brands->getData() as $brand)
        {
            $brandChoices[$brand->getStringName()] = $brand->getStringId();
        }

        $builder
            ->add('brand', ChoiceType::class, [
                'label' => 'Марка:',
                'required' => true,
                'choices' => $brandChoices,
                'placeholder' => 'Выберите марку...',
                'attr' => [
                    'class' => 'rounded-4 py-2 w-100 mb-0 form-select',
                    'id' => 'car_filter_form_brand',
                ],
            ]);

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function(FormEvent $event) {
                $form = $event->getForm();
                //                dd($form->getData());
                $brand = $form->getData() != null ? new CarBrandUid($form->getData()['brand']) : null;

                $models = $brand ? $this->carModelsByBrand->forBrand($brand)->findAll() : [];

                $modelsChoices = [];

                if($models != null)
                {
                    foreach($models->getData() as $model)
                    {
                        $modelsChoices[$model->getStringName()] = $model->getStringId();
                    }
                }

                $form->add('model', ChoiceType::class, [
                    'label' => 'Модель:',
                    'required' => true,
                    'choices' => $modelsChoices,
                    'placeholder' => empty($modelsChoices) ? 'Сначала выберите марку' : 'Выберите модель...',
                    'disabled' => empty($modelsChoices),
                    'attr' => [
                        'class' => 'rounded-4 py-2 w-100 mb-0 form-select',
                        'id' => 'car_filter_form_model',
                    ],
                ]);

                $model = $form->getData() != null ? new CarModelUid($form->getData()['model']) : null;
                $modelPetrols = $model ? $this->carModelPetrolsByModel->forModel($model)->findAll() : [];

                $modelPetrolsChoices = [];

                if($modelPetrols != null)
                {
                    foreach($modelPetrols->getData() as $modelPetrol)
                    {
                        $modelPetrolsChoices[$modelPetrol->getGenerationName()
                        .' '
                        .$modelPetrol->getName()
                        .' '
                        .$modelPetrol->getHp()
                        .' HP'] = $modelPetrol->getStringId();
                    }
                }

                $form->add('model_petrol', ChoiceType::class, [
                    'label' => 'Модификация:',
                    'required' => true,
                    'choices' => $modelPetrolsChoices,
                    'placeholder' => empty($modelPetrolsChoices) ? 'Сначала выберите модель' : 'Выберите модификацию...',
                    'disabled' => empty($modelPetrolsChoices),
                    'attr' => [
                        'class' => 'rounded-4 py-2 w-100 mb-0 form-select',
                        'id' => 'car_filter_form_model_petrol',
                    ],
                ]);
            },
        );

        $builder
            //            ->add('model_petrol', ChoiceType::class, [
            //                'label' => 'Модификация:',
            //                'required' => true,
            //                'choices' => [], // Будет заполняться через AJAX
            //                'placeholder' => 'Сначала выберите модель',
            //                'disabled' => true,
            //                'attr' => [
            //                    'class' => 'rounded-4 py-2 w-100 mb-0 form-select',
            //                    'id' => 'car_filter_form_model_petrol',
            //                ],
            //            ])
            //            ->add('cars_filter', ButtonType::class, [
            //                'label' => 'Подобрать шины',
            //                'attr' => [
            //                    'class' => 'btn-danger w-100 h-100 rounded-4 text-uppercase fw-bolder btn',
            //                    'id' => 'car_filter_form_cars_filter',
            //                    'disabled' => 'disabled',
            //                ],
            //            ])
            ->add('_token', HiddenType::class, [
                'data' => '', // CSRF токен будет добавлен в шаблоне
            ]);


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
                'action' => '/auto',
            ],
        ]);
    }
}