<?php

namespace App\Form;

use App\Entity\Blog;
use App\Entity\BlogCategories;
use Doctrine\DBAL\Types\BooleanType;
use JsonException;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogType extends AbstractType
{

    /**
     * @throws JsonException
     */
    public function buildForm (FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('coverImage', FileType::class, [
                'mapped' => false,   // we'll move the file in the controller
                'required' => false,
                'constraints' => [
                    new Image(maxSize: '4M', mimeTypesMessage: 'Please upload a valid image'),
                ],
            ])
            ->add('title', TextType::class, ['required' => true, 'label' => 'Title'])
            ->add('readTime', IntegerType::class, [
                'required' => true,
                'label' => 'Estimated read time (3 min)',
                'data' => 3,
                'attr' => [
                    'min' => 1,
                    'max' => 60,
                    'step' => 1,
                ],
            ])
            ->add('categories', EntityType::class, [
                'class' => BlogCategories::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'by_reference' => false,
                'attr' => [
                    'class' => 'form-select',
                    'multiple' => 'multiple',
                ],
            ])
            ->add('blogLanguage', TextType::class, ['required' => true, 'label' => "Blog's Language (English)"])
            ->add('content', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'js-editorjs',
                    'hidden' => true,
                ],
            ])
            ->add('is_published', CheckboxType::class, [
                'required' => false,
                'label' => 'Published',
                'help' => 'When checked, your blog will be visible publicly.',
            ])
            ->add('save', SubmitType::class);

    }

    public function configureOptions (OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Blog::class,
            'categories' => [],
        ]);
    }
}
