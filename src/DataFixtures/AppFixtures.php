<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Etape;
use App\Entity\Ingredient;
use App\Entity\Recette;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
      $faker = Faker\Factory::create('FR - fr');
      $slugify = new Slugify();

      for ($i =0; $i <= 10; $i++) {
        $recette = new Recette();
        $title = $faker->word;
        $slug = $slugify->slugify ( $title );
        $recette->setTitle ($title)
          ->setCover ('https://loremflickr.com/520/340/recipe')
          ->setAuteur ($faker->name)
          ->setNombre (4)
          ->setSlug ($slug)
          ->setDifficulte (mt_rand (1, 5));
        $manager->persist ($recette);


        for ($j=0; $j<=10; $j++){
          $etape = new Etape();
          $etape->setRecette ($recette)
                ->setDescription ($faker->paragraph);
          $manager->persist ($etape);
          }

        for ($k = 0; $k<=10; $k++){
          $ingredient = new Ingredient();
          $ingredient->setTitle ($faker->word)
                      ->setQuantite (mt_rand (3, 12))
                      ->setRecette ($recette);
          $manager->persist ($ingredient);
          }

    }
      $cat = array("Entrée", "Plat principal", "Dessert", "Encas", "Ptit déj");
      foreach ($cat as $value){
        $categorie = new Categorie();
        $categorie->setTitle ($value);
        $manager->persist ($categorie);
      }

        $manager->flush();
    }
}
