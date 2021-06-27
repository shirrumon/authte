<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\CallbackTransformer;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('name')
            ->add('surname')
            ->add('pesel')
            ->add('lang')
            ->add('date', BirthdayType::class)
            ->add('isVerified')
            ->add('roles', TextType::class)
            ->add('password');
              $builder->get('roles')
                  ->addModelTransformer(new CallbackTransformer(
                      function ($tagsAsArray) {
                          // transform the array to a string
                          return implode(', ', $tagsAsArray);
                      },
                      function ($tagsAsString) {
                          // transform the string back to an array
                          return explode(', ', $tagsAsString);
                      }
                  ));


    }



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
