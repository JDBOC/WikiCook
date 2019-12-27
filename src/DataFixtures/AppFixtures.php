<?php

  namespace App\DataFixtures;

  use App\Entity\Categorie;
  use App\Entity\Comment;
  use App\Entity\Etape;
  use App\Entity\Ingredient;
  use App\Entity\Recette;
  use App\Entity\Role;
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
      $adminRole = new Role();
      $adminRole->setTitle ( 'ROLE_ADMIN' );
      $manager->persist ( $adminRole );

      //gestion Administrateur
      $adminUser = new User();
      $adminUser  ->setSurnom ( 'Jedi' )
                  ->setEmail ( 'jdboc@live.fr' )
                  ->setPicture ( 'https://media.licdn.com/dms/image/C5103AQHftIGR4eua3Q/profile-displayphoto-shrink_200_200/0?e=1582156800&v=beta&t=Ya6vhdil2WwO35fpOzpGjftxp_mmAcnu0sONn4BfuKw' )
                  ->setHash ( $this->encoder->encodePassword ( $adminUser , 'password' ) )
                  ->setDescription ( '<p>' . join ( '</p><p>' , $faker->paragraphs ( 3 ) ) . '</p>' )
                  ->addUserRole ( $adminRole );
      $manager->persist ( $adminUser );


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
        $pictureId = $faker->numberBetween ( 1 , 99 ) . '.jpg';
        $picture = $picture . ($genre == 'male' ? 'men/' : 'women/') . $pictureId;
        $hash = $this->encoder->encodePassword ( $user , 'password' );
        $user ->setSurnom ( $faker->firstName ( $genre ) )
              ->setEmail ( $faker->email )
              ->setHash ( $hash )
              ->setDescription ( '<p>' . join ( '</p><p>' , $faker->paragraphs ( 3 ) ) . '</p>' )
              ->setPicture ( $picture );

        $manager->persist ( $user );
        $users[] = $user;
      }


      for ($i = 1; $i <= 60; $i++) {
        $recette = new Recette();
        $randomCat = $faker->randomElement ( $categories );
        $title = $faker->word;
        $slug = $slugify->slugify ( $title );
        $user = $users[mt_rand ( 0 , count ( $users ) - 1 )];
        $recette  ->setTitle ( $title )
                  ->setCover ( 'https://loremflickr.com/520/340/recipe' )
                  ->setCategorie ( $randomCat )
                  ->setNombre ( 4 )
                  ->setSlug ( $slug )
                  ->setDifficulte ( mt_rand ( 1 , 5 ) )
                  ->setAuthor ( $faker->randomElement ( $users ) );
        $manager->persist ( $recette );

        // Gestion des Commentaires

        if (mt_rand ( 0 , 1 )) {
          $comment = new Comment();
          $comment  ->setContenu ( $faker->paragraph )
                    ->setNote ( mt_rand ( 1 , 5 ) )
                    ->setRecette ( $recette )
                    ->setAuthor ( $faker->randomElement ( $users ) );
          $manager->persist ($comment);
        }


        for ($j = 0; $j <= 10; $j++) {
          $etape = new Etape();
          $etape  ->setRecette ( $recette )
                  ->setDescription ( $faker->paragraph );
          $manager->persist ( $etape );
        }

        for ($k = 0; $k <= 10; $k++) {
          $ingredient = new Ingredient();
          $ingredient ->setTitle ( $faker->word )
                      ->setQuantite ( mt_rand ( 3 , 12 ) )
                      ->setRecette ( $recette );
          $manager->persist ( $ingredient );
        }

      }


      $manager->flush ();
    }
  }
