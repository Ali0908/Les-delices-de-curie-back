<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Difficulty;
use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',  TextType::class, [
            'label' => "Titre de la recette",
        ])
            ->add('description')
            ->add('steps')
            ->add( 'duration', null, [
            'help' => 'Durée en minutes',
            'label' => 'Durée de la recette'
        ])
            ->add('image', UrlType::class, [
            'help' => 'Url de l\'image'
        ])
            ->add('tags')
            ->add('categories' ,EntityType::class, [
                'label' => 'Choisir la catégorie',
                'choice_label' => 'name', // valeur de la prop à afficher dans les balises options
                'class' => Category::class,
                'multiple' => true,
                'expanded' => true,
                'required' => false
            ])
            ->add('difficulties', EntityType::class, [
                'class' => Difficulty::class,
                'multiple' => false,
                'expanded' => false
            ])
            ->add('users', TextType::class)
            ->add('likes')
            ->add('favorites')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
