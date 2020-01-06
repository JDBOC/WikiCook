<?php

	namespace App\Service;

  use Doctrine\Common\Persistence\ObjectManager;
  use Doctrine\ORM\EntityManagerInterface;

  class Pagination {
	  private $entityClass;
	  private $limit = 10;
	  private $currentPage = 1;
    private $manager;

    public function __construct(EntityManagerInterface $entityManager)
    {

      $this->manager = $entityManager;
    }

    public function getData()
    {
      $offset = $this->currentPage * $this->limit - $this->limit;
      $repo = $this->manager->getRepository ($this->entityClass);
      $data = $repo->findBy ([], [], $this->limit, $offset);
      return $data;
    }

    public function getPage()
    {
      return $this->currentPage;
    }


    public function setPage($page)
    {
      $this->currentPage = $page;
      return $this;
    }

    public function getLimit(): int
    {
      return $this->limit;
    }

    public function setLimit($limit)
    {
      $this->limit = $limit;
      return $this;
    }


    public function getEntityClass()
    {
      return $this->entityClass;
    }


    public function setEntityClass($entityClass)
    {
      $this->entityClass = $entityClass;
      return $this;
    }

  }