<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
<<<<<<< HEAD
use Symfony\Component\Form\Extension\Core\Type\NumberType;
=======
>>>>>>> f439d50 (Mercredi 11 octobre 18h20 - Fin de journée :)
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
                'row_attr' => [
                    'class' => 'col-lg-5 mx-lg-3 my-lg-4'
                ]
            ])
            ->add('dateHeureDebut', DateTimeType::class,[
                'label' => 'Date et heure de la sortie :',
                'required' => true,
                'widget' => 'single_text',
                'row_attr' => [
                    'class' => 'col-lg-5 mx-lg-3 my-lg-4'
                ]
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'label' => 'Date limite d\'inscription :',
                'required'=> true,
                'widget' => 'single_text',
                'row_attr' => [
                    'class' => 'col-lg-5 mx-lg-3 my-lg-4'
                ]
            ])
            ->add('nbInscriptionsMax', NumberType::class, [
                'label' => 'Nombre de places :',
                'required' => true,
                'row_attr' => [
                    'class' => 'col-lg-5 mx-lg-3 my-lg-4'
                ]
            ])
            ->add('duree', NumberType::class, [
                'label' => 'Durée :',
                'required' => false,
                'row_attr' => [
                    'class' => 'col-lg-5 mx-lg-3 my-lg-4'
                ]
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description et infos :',
                'required' => true,
                'row_attr' => [
                    'class' => 'col-lg-5 mx-lg-3 my-lg-4'
                ]
            ])

            ->add('Ville', EntityType::class, [
                'label' => 'Ville :',
                'class' => Ville::class,
                'choice_label' => 'nom',
                'mapped' => false,
                'query_builder' => function (VilleRepository $villeRepository){
                    return $villeRepository->createQueryBuilder('c')
                        ->addOrderBy('c.nom')
                        ->getParameter('id');
                }
            ])

            ->add('lieu', EntityType::class, [
                'label' => 'Lieu :',
                'class' => Lieu::class,
                'choice_label' => 'nom',
            ])


            ->add('latitude', TextType::class, [
                'label' => 'Latitude :',
                'required' => false,
                'mapped' => false
            ])

            ->add('longitude', TextType::class, [
                'label' => 'Longitude :',
                'required' => false,
                'mapped' => false
            ])

            ->add('submit', SubmitType::class, [
                'label'=>"Enregistrer"
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
