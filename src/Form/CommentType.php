<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextType::class,[
                "label" => "Content",
                "attr" => [
                    "placeholder" => "Your review of the game"
                ]
            ])
            ->add('id_user', EntityType::class,[
                'class' => User::class,
                'label' => 'Your name',
                'expanded' => false,
                'multiple' => false,
                'choice_label' => 'name'
            ])
            ->add('id_game', EntityType::class,[
                'class' => Game::class,
                'label' => 'Name of the Game',
                'expanded' => true,
                'multiple' => false,
                'choice_label' => 'name'
            ])
            ->add('created_at', DateType::class,[
                "label" => 'Date du commentaire',
                'input' => 'datetime_immutable',
                "widget" => 'single_text',
                'row_attr' => ['class' => 'col-2']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
