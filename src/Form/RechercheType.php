<?php

  namespace App\Form;

  use App\Entity\Categorie;
  use Symfony\Bridge\Doctrine\Form\Type\EntityType;
  use Symfony\Component\Form\AbstractType;
  use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
  use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
  use Symfony\Component\Form\Extension\Core\Type\SearchType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;
  use Symfony\Component\Form\Extension\Core\Type\TextType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\OptionsResolver\OptionsResolver;

  class RechercheType extends CommonType
  {
    public function buildForm(FormBuilderInterface $builder , array $options)
    {
      $builder
        ->add ( 'recherche' , TextType::class , $this->getConfig ( ' ' , "indiquez une recette ou un ingrédient" ) )

        ->add ( 'soumettre' , SubmitType::class );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
      $resolver->setDefaults ( [
        // Configure your form options here
      ] );
    }
  }