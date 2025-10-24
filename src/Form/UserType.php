<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\UserProfile;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm (FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Username',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'required' => true,
            ])
            ->add('roles', ChoiceType::class, [
                'required' => true,
                'label' => 'Role',
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'Blogger' => 'ROLE_BLOGGER',
                    'User' => 'ROLE_USER',
                ],
                'multiple' => false,   // ⬅️ only one value
                'expanded' => false,   // ⬅️ renders as a <select>
            ])
            ->add('terms', CheckboxType::class, ['label' => 'I agree to the Terms of Service', 'required' => true])
            ->add('save', SubmitType::class, [
                'label' => 'Create User',
            ]);

        // ✅ Convert between string (from form) and array (in entity)
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform array to string
                    return count($rolesArray) ? $rolesArray[0] : null;
                },
                function ($rolesString) {
                    // transform string back to array
                    return $rolesString ? [$rolesString] : [];
                }
            ));
    }

    public function configureOptions (OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
