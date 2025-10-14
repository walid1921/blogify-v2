<?php

namespace App\Form;

use App\Entity\Blog;
use Symfony\Component\Form\AbstractType;
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
    public function buildForm (FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['required' => true, 'label' => 'Title'])
            ->add('content', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'js-editorjs',
                    'hidden' => true,
                ],
            ])
            ->add('coverImage', FileType::class, [
                'mapped' => false,   // we'll move the file in the controller
                'required' => false,
                'constraints' => [
                    new Image(maxSize: '4M', mimeTypesMessage: 'Please upload a valid image'),
                ],
            ])
            ->add('is_published')
            ->add('readTime', IntegerType::class, ['label' => 'Estimated read time (min)'])
            ->add('blogLanguage', TextType::class, ['required' => true])
            ->add('save', SubmitType::class);
    }

    public function configureOptions (OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Blog::class,
        ]);
    }
}
