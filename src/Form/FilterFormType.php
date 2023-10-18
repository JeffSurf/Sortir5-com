<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Repository\SiteRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,[
                'required' => false,
                'attr' => ['placeholder' => 'Rechercher par mot-clé'],
            ])

            ->add('site', EntityType::class, [
                'placeholder' => "Choisir un site",
                'required' => false,
                'mapped' => false,
                'class' => Site::class,
                'query_builder' => function (SiteRepository $siteRepository): QueryBuilder {
                    return $siteRepository->createQueryBuilder('s')
                        ->orderBy('s.nom', 'ASC');
                },
                'choice_label' => 'nom',
                "data" => $options["user"]->getSite(),
            ])

            ->add('etat', ChoiceType::class, [
                'placeholder' => "Choisissez un état",
                'choices' => Etat::cases(),
                'choice_label' => function(?Etat $etat) {
                    return null === $etat ? 'blank' : $etat->value;
                },

                'choice_value' => function(?Etat $etat) {
                    return null === $etat ? 'blank' : $etat->name;
                },
                'required' => false,
                'mapped' => false
            ])

            ->add('dateDebut', DateTimeType::class,[
                'label' => 'Date de début :',
                'required' => false,
                'widget' => 'single_text',
                'mapped' => false
            ])
            ->add('dateFin', DateTimeType::class, [
                'label' => 'Date de fin :',
                'required'=> false,
                'widget' => 'single_text',
                'mapped' => false
            ])
            ->add('estOrganise', CheckboxType::class, [
                'label'=> 'Sorties dont je suis l\'organisateur.trice',
                'required' => false,
                'mapped' => false,
                "attr" => ["checked" => true]
            ])

            ->add('estInscrit', CheckboxType::class, [
                'label'=> 'Sorties auxquelles je suis inscrit.e',
                'required' => false,
                'mapped' => false,
                "attr" => ["checked" => true]
            ])

            ->add('estPasInscrit', CheckboxType::class, [
                'label'=> 'Sorties auxquelles je ne suis pas inscrit.e',
                'required' => false,
                'mapped' => false,
                "attr" => ["checked" => true]
            ])

            ->add('estPassee', CheckboxType::class, [
                'label'=> 'Sorties passées',
                'required' => false,
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //'data_class' => Sortie::class
            'method' => 'GET',
            'csrf_protection' => false
        ]);

        $resolver->setRequired("user");
    }
}
