<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserType extends AbstractType
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Enter your email',
                ]

            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    
                    'Admin' => 'ROLE_ADMIN',
                    'TEACHER' => 'ROLE_TEACHER',
                    'STUDENT' => 'ROLE_STUDENT',
                ],
                'multiple' => true, // Allow selecting multiple roles
                'expanded' => true, // Display roles as checkboxes
            ])
            ->add('password', PasswordType::class, [
                'required' => true,
                'attr' => [
                    // 'autocomplete' => 'new-password', // This is to hint to the browser that it should not suggest previous passwords
                    // 'placeholder' => '********',      // This is to display asterisks as a placeholder
                ]
            ])
            ->add('username')
            // ->add('created_at')
            // ->add('updated_at')
            ->add('Submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
