<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', null, [
                "required" => true
            ])
            ->add('imageProfil', FileType::class, [
                "label" => "Image de profil",
                "mapped" => false,
                "required" => false,
                "help" => "Format (.png, .jpg, .jpeg)<br>Taille maxi (2MB)",
                'help_html' => true,
                "attr" => ["class" => "image-file"],
                "constraints" => [
                    new File([
                        "maxSize" => "2048k",
                        "maxSizeMessage" => "Le fichier est trop volumineux ({{ size }} {{ suffix }}). Taille maximale {{ limit }} {{ suffix }}",
                        "mimeTypes" => [
                            "image/png",
                            "image/jpg",
                            "image/jpeg"
                        ],
                        "mimeTypesMessage" => "Le type {{ type }} est invalide. Les formats valides sont {{ types }}"
                    ])
                ]
            ])
            ->add('rgpd', CheckboxType::class, [
                'label' => "Accepter que les utilisateurs autorisés à afficher le profil puissent voir la photo",
                'required' => false
            ])
            ->add('prenom', null, [
                "required" => true
            ])
            ->add('nom', null, [
                "required" => true
            ])
            ->add('email', EmailType::class, [
                "required" => true
            ])
            ->add('telephone', TelType::class, [
                "required" => false
            ])
            ->add("site", EntityType::class, [
                'label' => 'Site du participant',
                "class" => Site::class,
                "choice_label" => "nom"
            ])
            ->add('actif', CheckboxType::class, [
                'mapped' => false,
                'label' => "L'utilisateur est actif",
                'data' => true,
                'required' => false,
            ])
            ->add('mdp', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'label' => "Mot de passe",
                'attr' => ['autocomplete' => 'new-password'],
                'help' => 'Par défaut : nomprenom@S2023',
                'constraints' => [
                    new Length([
                        'min' => 3, 'minMessage' => 'Le mot de passe doit faire au moins {{ limit }} caractères',
                        'max' => 4096, 'maxMessage' => 'Le mot de passe ne peut pas excéder {{ limit }} caractères'
                    ])
                ],
            ])
            ->add('valider', SubmitType::class, ['attr' => ['class' => 'btn btn-outline-primary']])
            ->add('annuler', ButtonType::class, ['attr' => ['class' => 'btn btn-outline-danger']])
        ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
