<?php

namespace App\Model;

use App\Model\Entities\Team;

/*
 * Player management
 */

class TeamManager
{
    const TEAM_ENTITY = '\App\Model\Entities\Team';
    
    public function __construct(\Kdyby\Doctrine\EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getTeams()
    {
        return $this->em->getRepository($this::TEAM_ENTITY)->findAll();
    }
    
    public function getTeamById($id)
    {
        return $this->em->find($this::TEAM_ENTITY, $id);
    }
}
