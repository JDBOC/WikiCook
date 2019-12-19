<?php

  namespace App\DataFixtures;

  use App\Entity\Categorie;
  use App\Entity\Etape;
  use App\Entity\Ingredient;
  use App\Entity\Recette;
  use App\Entity\User;
  use Cocur\Slugify\Slugify;
  use Doctrine\Bundle\FixturesBundle\Fixture;
  use Doctrine\Common\Persistence\ObjectManager;
  use Faker;
  use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

  class AppFixtures extends Fixture
  {
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
      $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
      $faker = Faker\Factory::create ( 'FR - fr' );
      $slugify = new Slugify();


      //gestion utilisateurs
      $users = [];
      $genres = ['male' , 'female'];
      $cat = array("Entrée" , "Plat principal" , "Dessert" , "Encas" , "Ptit déj");

$categories = [];
      foreach ($cat as $value) {
        $categorie = new Categorie();
        $categorie->setTitle ( $value );

        $manager->persist ( $categorie );
        $categories [] = $categorie;
      }


      for ($i = 1; $i <= 10; $i++) {
        $user = new User();
        $genre = $faker->randomElement ( $genres );

        $picture = 'https://randomuser.me/portraits/';
        $pictureId = $faker->numberBetween (1, 99) . '.jpg';
        $picture = $picture . ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

        $hash = $this->encoder->encodePassword ($user, 'password');

        $user ->setSurnom ( $faker->firstName ( $genre ) )
              ->setEmail ( $faker->email )
              ->setHash ( $hash )
              ->setPicture ($picture);

        $manager->persist ( $user );
        $users[] = $user;
      }


      for ($i = 0; $i <= 30; $i++) {
        $recette = new Recette();
        $randomCat = $faker->randomElement ($categories);
        $title = $faker->word;
        $slug = $slugify->slugify ( $title );

        $user = $users[mt_rand ( 0 , count ( $users ) - 1 )];

        $recette->setTitle ( $title )
          ->setCover ( 'https://loremflickr.com/520/340/recipe' )
          ->setAuteur ( $faker->name )
          ->setCategorie ($randomCat)
          ->setNombre ( 4 )
          ->setSlug ( $slug )
          ->setDifficulte ( mt_rand ( 1 , 5 ) )
          ->setAuthor ( $faker->randomElement ($users) );
        $manager->persist ( $recette );


        for ($j = 0; $j <= 10; $j++) {
          $etape = new Etape();
          $etape->setRecette ( $recette )
            ->setDescription ( $faker->paragraph );
          $manager->persist ( $etape );
        }

        for ($k = 0; $k <= 10; $k++) {
          $ingredient = new Ingredient();
          $ingredient->setTitle ( $faker->word )
            ->setQuantite ( mt_rand ( 3 , 12 ) )
            ->setRecette ( $recette );
          $manager->persist ( $ingredient );
        }

      }

      $manager->flush ();
    }
  }
