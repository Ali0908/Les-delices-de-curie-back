<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user')
            ->add('email')
            ->add('password')
            ->add('roles', ChoiceType::class,
            [
                'choices' => [
                    'user' => 'ROLE_USER',
                    'admin' => 'ROLE_ADMIN',
                    'visitor' => 'PUBLIC_ACCESS',
                ],
                "multiple" => true,
                // radio buttons or checkboxes
                "expanded" => true
            ]
        )
            ->add('likes')
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                // je récupère le formulaire pour le modifier
                $formulaire = $event->getForm();

                // on récupère l'objet qui est lié au formulaire
                /** @var User $userEntity */
                $userEntity = $event->getData();

                if ($userEntity->getId() !== null) {
                    // j'ai un user qui est lié à mon formulaire
                    // je suis donc dans le cas d'un édition
                    // ici le password est optionnel

                    $formulaire->add('password', PasswordType::class, [
                        // Pour le form d'édition, on n'associe pas le password à l'entité
                        // @link https://symfony.com/doc/current/reference/forms/types/form.html#mapped
                        // avec cette option : le handleRequest() ne remplira pas le mot de passe dans l'entity user
                        'mapped' => false,
                        'attr' => [
                            'placeholder' => 'Laissez vide si inchangé'
                        ],
                        'constraints' => [
                            new NotBlank(),
                            new Regex(
                                "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/",
                                "Le mot de passe doit contenir au minimum 8 caractères, une majuscule, un chiffre et un caractère spécial"
                            ),
                        ]
                    ]);
                } else {
                    // j'ai un user qui n'a pas d'ID
                    // je suis donc dans le cas d'une création
                    // ici le password est obligatoire
                    // je peux donc ajouter au formulaire ce que je veux
                    $formulaire->add(
                        'password',
                        PasswordType::class,
                        [
                            // En cas d'erreur du type
                            // Expected argument of type "string", "null" given at property path "password".
                            // (notamment à l'edit en cas de passage d'une valeur existante à vide)
                            'empty_data' => '',
                            // On déplace les contraintes de l'entité vers le form d'ajout
                            'constraints' => [
                                new NotBlank(),
                                new Regex(
                                    "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/",
                                    "Le mot de passe doit contenir au minimum 8 caractères, une majuscule, un chiffre et un caractère spécial"
                                ),
                            ]
                        ]
                    );
                }
            });;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
