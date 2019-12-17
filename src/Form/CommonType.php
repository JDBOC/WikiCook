<?php

  namespace App\Form;


  use Symfony\Component\Form\AbstractType;

  class CommonType extends AbstractType {

    /**
     * Permet de configurer chaque champs du formulaire
     *
     * @param $label
     * @param $placeholder
     * param array $options
     * @param array $options
     * @return array
     */
    protected function getConfig($label, $placeholder, $options = []){
      return array_merge_recursive ([
        'label' => $label,
        'attr' => [
          'placeholder' => $placeholder
        ]
      ], $options);
    }
  }