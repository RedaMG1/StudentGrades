<?php

namespace App\Form;
use App\Entity\User;
use App\Entity\Grade;
use App\Entity\Exam;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class GradeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
             ->add('user',EntityType::class,[
                'class' =>User::class,
                'choice_label'=>'username',
             ])
             ->add('exam',EntityType::class,[
                'class' =>Exam::class,
                'choice_label'=>'name',
             ])
             ->add('grade')
            
            // ->add('category')
            ->add('Submit',SubmitType::class,[
                'attr' => [
                    'class' =>'btn btn-primary'
                ],])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Grade::class,
        ]);
    }
}
