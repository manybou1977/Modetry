<?php

namespace App\Form;

use App\Entity\Tailles;
use App\Entity\Produits;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProduitsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class)
            ->add('prix', NumberType::class)
            ->add('tailles',EntityType::class,[
                'class'=>Tailles::class,
                'choice_label'=>function($choix){
                    return 'Taille : '. $choix->getMesures();
                },
                'multiple'=>true,
                'expanded'=>true,
            ])
            ->add('stock',TextType::class)
            ->add('description',TextType::class)
            ->add('couleur',TextType::class)
            ->add('image', FileType::class, [
                'label' => 'Image produit',
                'required' => true,
                'data_class' => null,
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'image en format .jpg , .webp ou .png uniquement',
                    ])
                ],
            ])
            ->add('categorie',EntityType::class,[
                'class'=>Categorie::class,
                'choice_label'=>function($chox)
                {
                    return $chox->getNom() . " ".$chox->getGenre();
                },
                'multiple'=>false,
                'expanded'=>false,

            ])
            ->add('enregistrer',SubmitType::class,[
            'attr'=>[
                'class'=>'btn'
            ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
        ]);
    }
}
