<?php

namespace App\Model;

use App\Model\Entities\Season;

/*
 * Season management
 */

class SeasonManager
{
    public function __construct(\Kdyby\Doctrine\EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getSeasons()
    {
        $dao = $this->em->getRepository('\App\Model\Entities\Season');
        return $dao->findAll();
    }
    
    public function getSeasonById($id)
    {
        return $this->em->find('\App\Model\Entities\Season', $id);
    }
}
