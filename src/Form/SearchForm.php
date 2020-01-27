<?php


  namespace App\Form;


  use App\Data\SearchData;

  use App\Entity\Categorie;
  use Symfony\Bridge\Doctrine\Form\Type\EntityType;
  use Symfony\Component\Form\Extension\Core\Type\NumberType;
  use Symfony\Component\Form\Extension\Core\Type\TextType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\OptionsResolver\OptionsResolver;

  class SearchForm extends CommonType
  {

    public function buildForm(FormBuilderInterface $builder , array $options)
    {
      $builder
        ->add ('q', TextType::class,[
          'label' => false,
          'required' => false,
            'attr' => [
              'placeholder' => "Rechercher une recette ou un ingrédient"
            ]
          ])
        ->add ('categories', EntityType::class, [
          'label' => false,
          'required' => false,
          'class' => Categorie::class,
          'expanded' => true,
          'multiple' => true
        ])

        ->add ('max', NumberType::class, [
          'label' => false,
          'required' => false,
          'attr' => [
            'placeholder' => 'durée max'
          ]
        ])



      ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
      $resolver->setDefaults ([
        'data_class' => SearchData::class,
        'method' => 'GET',
        'csrf_protection' => false
      ]);
    }

    public function getBlockPrefix()
    {
      return '';
    }

  }