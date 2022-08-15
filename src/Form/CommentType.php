<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add( 'dateComment', DateType::class, [
            'label' => 'Crée le...',
            // Les années depuis le premier film à + 10 ans
            // https://symfony.com/doc/5.4/reference/forms/types/date.html#years
            'years' => range(1895, date('Y') + 10),
            //'widget' => 'single_text',
            'input' => 'datetime'
        ])
            ->add('contentComment')
            ->add( 'recipe', TextType::class, [
            'label' => "Titre de la recette",
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
