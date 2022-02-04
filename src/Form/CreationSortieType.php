<?php

namespace App\Form;

use App\Entity\Sortie;
use App\Entity\Ville;
use App\Entity\Lieu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreationSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',                    TextType::class,        [ 'label' => 'Nom de la sortie :'])
            ->add('dateHeureDebut',         DateTimeType::class,    [ 'label' => 'Date et heure de la sortie :',
                                                                                'widget' => 'single_text'])
            ->add('duree',                  IntegerType::class,     [ 'label' => 'Durée :'])
            ->add('dateLimiteInscription',  DateType::class,        [ 'label' => 'Date limite d"inscription :',
                                                                                'widget' => 'single_text',
                                                                                'format' => 'yyyy-MM-dd'])
            ->add('nbInscriptionsMax',      IntegerType::class,     [ 'label' => 'Nombre de places :'])
            ->add('infosSortie',            TextareaType::class,    [ 'label' => 'Description et infos :']);
//            ->add('ville',                  EntityType::class,      [ 'label' => 'Ville :',
//                                                                                'choice_label' => 'nom',
//                                                                                'class' => Ville::class,
//                                                                                'mapped' => false]);
//            ->add('lieu',                   EntityType::class,      [ 'label' => 'Lieu :',
//                                                                                'choice_label'=>'nom',
//                                                                                'class' => Lieu::class,
//                                                                                'mapped' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
