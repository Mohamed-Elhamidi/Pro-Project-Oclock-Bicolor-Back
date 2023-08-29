<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class,[
                "label" => "Email",
                "attr" => [
                    "placeholder" => "Mail of the user"
                ]
            ])

            ->add('name',TextType::class,[
                "label" => "Name",
                "attr" => [
                    "placeholder" => "Name of the user"
                ]
            ] );

            // Je souhaite ici ne pas afficher le champ password si je suis en mode edit
            // En effet un admin n'est pas censé pouvoir modifier le mot de passe de quelqu'un d'autre
            // Je vais donc utiliser une option custom_option qui me permettra de savoir si je suis en mode edit ou non
            // Les options customs ce definissent plus bas dans la méthode configureOptions
            // Pour les utiliser il suffit lors de la création du formulaire de passer un tableau en deuxième paramètre de la méthode createForm
            if($options["custom_option"] !== "edit"){
                $builder
                // Je souhaite ici utiliser le champ repeated pour avoir deux champs password
                // https://symfony.com/doc/current/reference/forms/types/repeated.html
                    ->add('password',RepeatedType::class,[
                        "help" => "4 characters minimum",
                        // D'ou ici l'interet de mettre une contrainte directement dans le form vu que le mot de passe s'affichera conditionnellement
                        "constraints" => [
                            new Length([
                                "min" => 4,
                                "minMessage" => "4 characters minimum"
                            ])
                        ],
                        "type" => PasswordType::class,
                        'invalid_message' => 'The password need to be identical',
                        'required' => true,
                        'first_options'  => ['label' => 'Your password',"attr" => ["placeholder" => "*****"]],
                        'second_options' => ['label' => 'Repeat your password',"attr" => ["placeholder" => "*****"]],
                    ]);
            }
            $builder
                ->add('roles',ChoiceType::class,[
                    "label" => "Role",
                    "choices" => [
                        
                        "Admin" => "ROLE_ADMIN"
                    ],
                    "multiple" => true,
                    "expanded" => true
                ])
          
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'custom_option' => "default",
        ]);
    }
}
