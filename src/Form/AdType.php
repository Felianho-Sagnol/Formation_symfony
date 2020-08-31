<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdType extends AbstractType
{

    /**
     * Undocumented function
     *
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     */
    private function getConfiguration($label,$placeholder,$options=[]){
        return array_merge( [
            'label' => $label,
            'attr' => [
                'placeholder'  => $placeholder
            ]
            ],$options);
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class,$this->getConfiguration('Titre','Donner le titre de votre annonce !'))
            ->add('slug',TextType::class,$this->getConfiguration('Adresse web','Donner l\'adresse web non obligatoire!',['required' => false]))
            ->add('introduction',TextType::class,$this->getConfiguration('Introduction','Donner une description global de votre annonce'))
            ->add('price',MoneyType::class,$this->getConfiguration('Prix par nuit','Donner le prix par nuit !'))
            ->add('content',TextareaType::class,$this->getConfiguration('Contenu','Donner une descriptiondétaillée de votre annonce !'))
            ->add('coverImage',UrlType::class,$this->getConfiguration('Url de l\'image','Donner l\'url de l\'image la plus atirante de votre annonce !'))
            ->add('rooms',IntegerType::class,$this->getConfiguration('Nombre de chambres','Donner le nombre de chambres disponibles de votre annonce !'))
            ->add(
                'images',
                CollectionType::class,
                [
                    'entry_type' => ImageType::class,
                    'allow_add' => true,
                    'allow_delete' => true
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
