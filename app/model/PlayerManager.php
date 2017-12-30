<?php

namespace App\Model;

use App\Model\Entities\Player;

/*
 * Player management
 */

class PlayerManager
{
    public function __construct(\Kdyby\Doctrine\EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getPlayers()
    {
        $dao = $this->em->getRepository('\App\Model\Entities\Player');
        return $dao->findAll();
    }
}