<?php


  namespace App\Data;


  use App\Entity\Categorie;

  class SearchData
  {
    /**
     * @var string
     */
    public $q = '';

    /**
     * @var Categorie[]
     */
    public $categories = [];

    /*
     * @var null|integer
     */
    public $max;

    /*
     * @var null|integer
     */
    public $min;


  }