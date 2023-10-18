<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfilFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => "Email",
                "empty_data" => ""
            ])
            ->add('nom', TextType::class, [
                "empty_data" => ""
            ])
            ->add('nom', TextType::class, [
                "empty_data" => ""
            ])
            ->add('prenom', TextType::class, [
                "empty_data" => ""
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => ['maxlength' => 10, 'placeholder' => '0xxxxxxxxx']

            ])
            ->add('pseudo', TextType::class, [
                'label' => "Pseudo",
                "empty_data" => ""
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'required' => true,
                'label' => "Mot de passe",
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez un mot de passe',
                    ]),
                ],
            ])
            ->add('image', FileType::class, [
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
                            "image/jpeg",
                            "image/svg+xml"
                        ],
                        "mimeTypesMessage" => "Le type {{ type }} est invalide. Les formats valides sont {{ types }}"
                    ])
                ]
            ])
            ->add('rgpd', CheckboxType::class, [
                "label" => "Accepter que les utilisateurs puissent voir votre photo",
                "required" => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
