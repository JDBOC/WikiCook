<?php

namespace App\Repository;

use App\Entity\Recette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Recette|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recette|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recette[]    findAll()
 * @method Recette[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecetteRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct ( $registry , Recette::class );
  }


  /**
   * @param $limit
   * @return Recette[] Returns an array of Recette objects
   */

  public function findByDate($limit)
  {
    return $this->createQueryBuilder ( 'r' )
      ->select ( 'r as recette, r.createdAt as c' )
      ->groupBy ( 'r' )
      ->orderBy ( 'c' , 'DESC' )
      ->setMaxResults ( $limit )
      ->getQuery ()
      ->getResult ();
  }


  public function findBySearch()
  {
    return $this->createQueryBuilder ('search')
                ->select ('recette as recette, r.title as t, r.ingredient as i')


                ->getQuery ()
                ->getResult ();
    }


}


