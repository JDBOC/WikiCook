<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Recette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
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
      ->select ( 'r')
      ->groupBy ( 'r' )
      ->orderBy ( 'r.createdAt' , 'DESC' )
      ->setMaxResults ( $limit )
      ->getQuery ()
      ->getResult ();
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

  /**
   * test mise en place recherche avancée
   * @return Recette[]
   */
  public function findSearch(SearchData $search): array
  {
    $query = $this
      ->createQueryBuilder ('r')
      ->select('c', 'r', 'n')
      ->join ('r.categorie', 'c')
      ->join ('r.comments', 'n');
    if (!empty($search->q)) {
      $query = $query
        ->andWhere ('r.title LIKE :q')
        ->setParameter ('q', "%{$search->q}%");
    }
    if (!empty($search->max)) {
      $query =$query
        ->andWhere ('r.duration <= :max')
        ->setParameter ('max', $search->max);
    }
    if (!empty($search->categories)) {
      $query = $query
        ->andWhere ('c.id IN (:categories)')
        ->setParameter ('categories', $search->categories);
    }
    if (!empty($search->note)){
      $query = $query
        ->andWhere ('n.note >= (:note)')
        ->setParameter ('note', $search->note);
    }
    return $query->getQuery ()->getResult ();
  }
}


