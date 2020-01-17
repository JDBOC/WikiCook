<?php

namespace App\Repository;

use App\Entity\Recette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

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


  public function findBySearch($recherche): Query
  {


    $qb = $this->createQueryBuilder('r');
        $qb->add('where', $qb->expr()->orX(
          $qb->expr()->like('r.title', $recherche['recherche']),
          $qb->expr()->like('r.ingredient', $recherche['recherche'])

        ))
          ->setParameter('recherche', $recherche['recherche'])
          ->orderBy('r.title', 'DESC')
        ;
        return $qb->getQuery();
    }

    public function findByRecherche($recherche)
    {
      return $this->createQueryBuilder ('r')
        ->leftJoin ('r.ingredient', 'i')
        ->where ('i.title LIKE :iTitle')
        ->setParameter ('iTitle', '%'.$recherche['recherche'].'%')
        ->orWhere ('r.title LIKE :rTitle')
        ->setParameter ('rTitle', '%'.$recherche['recherche'].'%')
        ->orderBy ('r.title', 'DESC')
        ->getQuery ()
        ->getResult ();


    }

}


