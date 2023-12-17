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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ModificationMonCompteFormType extends AbstractType
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
           
            ->add('telephone',TextType::class,[
                'label'=> 'Téléphone :'
            ])
            ->add('email', RepeatedType::class, [
                'type' => EmailType::class,
                'first_options' => ['label' => 'Adresse e-mail:'],
                'second_options' => ['label' => 'Confirmer l\'adresse e-mail:'],
            ])
            ->add('enregistrer',SubmitType::class,
            [
                'attr'=> [
                    'class'=>'btn-dark text-light btn-hover'
                ]
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
