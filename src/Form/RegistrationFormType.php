<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class,[
                'label'=>'Nom :'
            ])
            ->add('prenom',TextType::class,[
                'label'=>'Prénom :'
            ])
            ->add('pseudo',TextType::class,[
                'label'=>'Pseudo :'
            ])
            ->add('adresse',TextType::class,[
                'label'=>'Adresse :'
            ])
            ->add('code_postal',IntegerType::class,[
                'label'=> 'Code postal :'
            ])
            
            ->add('ville',TextType::class,[
                'label'=>'Ville :'
            ])
            ->add('dateNaissance', DateType::class,[
                'label' => '* Date de Naissance',
                'years' => range(date('Y') - 100, date('Y') + 0),
                'format' => 'dd-MM-yyyy',
     
            ])
            ->add('telephone',TextType::class,[
                'label'=> 'Téléphone :'
            ])
            ->add('email', RepeatedType::class, [
                'type' => EmailType::class,
                'first_options' => ['label' => 'Adresse e-mail:'],
                'second_options' => ['label' => 'Confirmer l\'adresse e-mail:'],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label'=> 'Accepter les CGV et CGU ',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => [
                    'label' => 'Mot de passe', // Label pour le champ "Mot de passe"
                    'attr' => ['autocomplete' => 'new-password'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez entrer un mot de passe',
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                            'max' => 4096,
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe', // Label pour le champ "Confirmer le mot de passe"
                    'attr' => ['autocomplete' => 'new-password'],
                ],
            ])
            
           ->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'mapped' => false,
            'first_options' => [
                'label' => 'Mot de passe', // Label pour le champ "Mot de passe"
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
            ],
            'second_options' => [
                'label' => 'Confirmer le mot de passe', // Label pour le champ "Confirmer le mot de passe"
                'attr' => ['autocomplete' => 'new-password'],
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
