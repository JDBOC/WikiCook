<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends CommonType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('surnom', TextType::class, $this->getConfig (" ","Votre Surnom"))
            ->add('email', EmailType::class, $this->getConfig (" ","Indiquez une adresse mail"))
            ->add('picture', UrlType::class, $this->getConfig (" ", "Indiquez l'url de votre photo"))
            ->add('hash', PasswordType::class, $this->getConfig (" ", "Indiquez un mot de passe"))
            ->add ('passwordConfirm', PasswordType::class, $this->getConfig (" ", "Confirmez votre mot de passe"))

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
