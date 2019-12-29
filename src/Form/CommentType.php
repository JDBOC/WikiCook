<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends CommonType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('note', IntegerType::class, $this->getConfig ('Note', "Donnez une note de 1 à 5 à cette recette", [
              'attr' => [
                'min' => 1,
                'max' => 5,
                'step' => 1
              ]
            ]))
            ->add('contenu', TextareaType::class, $this->getConfig ('Commentaire', "Dîtes nous ce que vous en avez pensé"))

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
