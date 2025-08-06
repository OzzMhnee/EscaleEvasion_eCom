<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\SubCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('city')
            ->add('departement')
            ->add('isAvailable')
            ->add('couchages')
            ->add('surface')
            ->add('imageFile', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
                'label' => 'Image (JPG, PNG, etc.)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Veuillez ajouter une image valide (JPG, PNG, GIF).',
                    ])
                ],
            ])
            ->add('imageFile2', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
                'label' => 'Image 2 (JPG, PNG, etc.)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Veuillez ajouter une image valide (JPG, PNG, GIF).',
                    ])
                ],
            ])
            ->add('imageFile3', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
                'label' => 'Image 3 (JPG, PNG, etc.)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Veuillez ajouter une image valide (JPG, PNG, GIF).',
                    ])
                ],
            ])
            ->add('imageFile4', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
                'label' => 'Image 4 (JPG, PNG, etc.)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Veuillez ajouter une image valide (JPG, PNG, GIF).',
                    ])
                ],
            ])
            ->add('imageFile5', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
                'label' => 'Image 5 (JPG, PNG, etc.)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Veuillez ajouter une image valide (JPG, PNG, GIF).',
                    ])
                ],
            ])
            ->add('isSwimmingPool', null, [
                'required' => false,
                'label' => 'Piscine'
            ])
            ->add('isBath', null, [
                'required' => false,
                'label' => 'Baignoire'
            ])
            ->add('isClim', null, [
                'required' => false,
                'label' => 'Climatisation'
            ])
            ->add('isLaveLinge', null, [
                'required' => false,
                'label' => 'Lave-linge'
            ])
            ->add('isSecheLinge', null, [
                'required' => false,
                'label' => 'Sèche-linge'
            ])
            ->add('isLaveVaisselle', null, [
                'required' => false,
                'label' => 'Lave-vaisselle'
            ])
            ->add('isChauffage', null, [
                'required' => false,
                'label' => 'Chauffage'
            ])
            ->add('startDate')
            ->add('endDate')
            ->add('description')
            ->add('price')
            ->add('subCategory', EntityType::class, [
                'class' => SubCategory::class,
                'choice_label' => 'name',
                'multiple' => true,
                'group_by' => function ($subCategory) {
                    return $subCategory->getCategory() ? $subCategory->getCategory()->getName() : 'Autres';
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
