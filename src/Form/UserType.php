<?php

namespace App\Form;

use App\Entity\User;
use DateTime;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\CallbackTransformer;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('pesel', NumberType::class)
            ->add('lang', TextType::class)
            ->add('date', BirthdayType::class)
            ->add('createDate', DateType::class)
            ->add('isVerified', BooleanType::class)
            ->add('roles', TextType::class)
            ->add('password', PasswordType::class);
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
