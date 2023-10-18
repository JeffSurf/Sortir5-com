<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
                'label' => 'Durée :',
                'required' => false
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description et infos :',
                'required' => true,
                "empty_data" => ""
            ])
            /*
            ->add('ville', EntityType::class, [
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
            */
            ->add('lieu', ChoiceType::class, [
                'label' => 'Lieu :'
            ])


            ->add('motifAnnulation', TextareaType::class, [
                'label'=> 'Modif d\'annulation :',
                'required' => false
            ])
        ;
        /*
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();

            $sortie = $event->getData();
            $ville = null !== $sortie->getLieu() ? $sortie->getLieu()->getVille() : null;

            $form->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'query_builder' => function (LieuRepository $repository) use ($ville) {
                    return $repository->createQueryBuilder('l')
                        ->where('l.ville = :ville')
                        ->setParameter('ville', $ville)
                        ->orderBy('l.nom', 'ASC');
                },
            ]);
        });

        $builder->get('ville')->addEventListener(
            FormEvents::POST_SUBMIT,
                function(FormEvent $event, LieuRepository $lieuRepository){
                    @var Ville $ville
                    $ville = $event->getForm()->getNormData();

                    $lieux = $lieuRepository->findBy(['ville' => $ville]);

                    $lieuField = $event->getForm()->getParent()->get('lieu');

                    $lieuField->getConfig()->getOptions()['choices'] = $lieux;
                }
        );
        */
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
