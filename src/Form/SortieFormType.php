<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,[
                'label' => 'Nom de la sortie :',
                'required' => true,
                "empty_data" => ""
            ])
            ->add('dateHeureDebut', DateTimeType::class,[
                'label' => 'Date et heure de la sortie :',
                'required' => true,
                'widget' => 'single_text',
                "empty_data" => ""
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'label' => 'Date limite d\'inscription :',
                'required'=> true,
                'widget' => 'single_text',
                "empty_data" => ""
            ])
            ->add('nbInscriptionsMax', NumberType::class, [
                'label' => 'Nombre de places :',
                'required' => true
            ])
            ->add('duree', NumberType::class, [
                'label' => 'Durée (en minutes) :',
                'required' => false
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description et infos :',
                'required' => true,
                "empty_data" => ""
            ])

            ->add('lieu', EntityType::class, [
                'label' => 'Lieu :',
                "class" => Lieu::class,
                "choice_label" => "nom"
            ])

            ->add('motifAnnulation', TextareaType::class, [
                'label'=> 'Modif d\'annulation :',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
