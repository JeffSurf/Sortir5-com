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
            ->add('nbInscriptionsMax', TextType::class, [
                'label' => 'Nombre de places :',
                'required' => true,
                'row_attr' => [
                    'class' => 'col-lg-5 mx-lg-3 my-lg-4'
                ]
            ])
            ->add('duree', TextType::class, [
                'label' => 'Durée :',
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

            /*
            ->add('site', EntityType::class, [
                'label' => 'Ville organisatrice',
                'class' => Site::class,
            ])
            */

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
                'mapped' => false
            ])

            ->add('longitude', TextType::class, [
                'label' => 'Longitude :',
                'mapped' => false
            ])

            ->add('submit', SubmitType::class, [
                'label'=>"Enregistrer"
            ])

            //->add('etat')
            //->add('lieu')
            //->add('organisateur')
            //->add('participants')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
