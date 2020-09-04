<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName',TextType::class,$this->getConfiguration('Prenom','Votre Prenom ici ...'))
            ->add('lastName',TextType::class,$this->getConfiguration('Nom','Votre Nom de famille ici ...'))
            ->add('email',EmailType::class,$this->getConfiguration('Email','Votre Adresse emeil ici ...'))
            ->add('picture',UrlType::class ,$this->getConfiguration('Photo de profile','Url de votre avatar'))
            ->add('hash',PasswordType::class,$this->getConfiguration('Mot de passe','Choisisssez votre mot de passe ...'))
            ->add('passwordConfirm',PasswordType::class,$this->getConfiguration('Confirmation du mot de passe','Veillez confirmer votre mot de passe ...'))
            ->add('introduction',TextType::class,$this->getConfiguration('Introduction','Presenter vous !'))
            ->add('description',TextareaType::class,$this->getConfiguration('Description Détaillée','C\'est le moment de vous presenter en détaille'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
