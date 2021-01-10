<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'constraints' => new Length(null,2,40),
                'attr' => [
                   'placeholder' => 'Saisir votre prénom'
                    ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'constraints' => new Length(null,2,40),
                'attr' => [
                    'placeholder' => 'Saisir votre nom'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => "Email",
                'constraints' => new Length(null,5,40),
                'attr' => [
                    'placeholder' => 'Saisir votre Email'
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passes ne sont pas identiques',
                'required' => true,
                'label' => 'Mot de passe',
                'first_options' => ['label' => 'Mot de passe', 'attr' =>[
                    'placeholder' => 'Votre mot de passe'
                ]],
                'second_options' => ['label' => 'Confirmation mot de passe', 'attr' =>[
                    'placeholder' => 'Confirmez votre mot de passe'
                ]]
            ])
            ->add('submit', SubmitType::class, [
                'label' => "S'inscrire"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
