<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                "empty_data" => ""
            ])
            ->add('prenom', TextType::class, [
                "empty_data" => ""
            ])
            ->add('pseudo', TextType::class, [
                "empty_data" => ""
            ])
            ->add('telephone', TelType::class, ["required" => false])
            ->add('email', EmailType::class, [
                "empty_data" => ""
            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'options' => ['attr' => ['class' => 'password-field']],
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe doit être identitque',
                'required' => true,
                'attr' => ['autocomplete' => 'new-password'],
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Répétez le mot de passe'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez un mot de passe',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le mot de passe doit faire au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add("site", EntityType::class, [
                "class" => Site::class,
                "choice_label" => "nom"
            ])
            ->add('image', FileType::class, [
                "label" => "Image de profil",
                "attr" => ["class" => "image-file"],
                "mapped" => false,
                "required" => false,
                "help" => "Taille maxi (2MB)",
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
            ->add("rgpd", CheckboxType::class, [
                "label" => "Accepter que les utilisateurs puissent voir votre photo"
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
