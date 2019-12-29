<?php

  namespace App\Form;

  use App\Entity\Categorie;
  use App\Entity\Recette;
  use Symfony\Bridge\Doctrine\Form\Type\EntityType;
  use Symfony\Component\Form\AbstractType;
  use Symfony\Component\Form\Extension\Core\Type\CollectionType;
  use Symfony\Component\Form\Extension\Core\Type\IntegerType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\OptionsResolver\OptionsResolver;

  class RecetteType extends CommonType
  {
    public function buildForm(FormBuilderInterface $builder , array $options)
    {
      $builder
        ->add ( 'title' )
        ->add ( 'cover' )
        ->add ('duration', IntegerType::class,$this->getConfig ('durée', "indiquez la durée nécessaire en minutes"))
        ->add ( 'difficulte' )
        ->add ('nombre', $this->getConfig ('nombre', "c'est pour combien de personnes"))
        ->add ( 'categorie' , EntityType::class , [
          'class' => Categorie::class ,
          'choice_label' => 'title'
        ] )
        ->add ( 'ingredient' , CollectionType::class , [
          'entry_type' => IngredientType::class ,
          'allow_add' => true ,
          'allow_delete' => true ,
          'prototype' => true ,
          'by_reference' => false

        ] )
        ->add ('etape', CollectionType::class, [
          'entry_type' => EtapeType::class,
          'allow_add' => true,
          'allow_delete' => true,
          'prototype' => true,
          'by_reference' => false
        ])
      ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
      $resolver->setDefaults ( [
        'data_class' => Recette::class ,
        [
          'entry_type' => IngredientType::class

        ], [
          'entry_type' => EtapeType::class
        ]
      ] );
    }
  }
