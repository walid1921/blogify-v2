<?php

namespace App\Form;

use App\Entity\BlogCategories;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class BlogCategoriesType extends AbstractType
{
    public function buildForm (FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Category Name',
                'constraints' => [
//                    new NotBlank(['message' => 'Category name cannot be empty.']),  // It was already used in the BlogCategories Entity
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Category name must be at least {{ limit }} characters long.',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'button-primary button-primary--fullWidth mt-4']
            ]);
    }

    public function configureOptions (OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BlogCategories::class,
        ]);
    }
}
