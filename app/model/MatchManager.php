<?php

namespace App\Model;

use Nette\Application\UI;
use App\Model\Entities\Match;

/*
 * Match management
 */

class MatchManager
{
    const MATCH_ENTITY = '\App\Model\Entities\Match';
    
    public function __construct(\Kdyby\Doctrine\EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getMatches()
    {
        return $this->em->getRepository($this::MATCH_ENTITY)->findBy(array());
    }
    
    public function getMatchById($id)
    {
        return $this->em->find($this::MATCH_ENTITY, $id);
    }
    
    public function deleteMatchById($id)
    {
        $match = $this->em->find($this::MATCH_ENTITY, $id);
        $this->em->remove($match);
        $this->em->flush();
    }
    
    public function getLastRoundForSeason($season)
    {
        $dql = "SELECT MAX(m.round) AS round FROM \App\Model\Entities\Match m " .
               "WHERE m.season = ?1";
        return $this->em->createQuery($dql)
                ->setParameter(1, $season)
                ->getSingleScalarResult();
    }


    public function getLastRoundMatchesForSeason($season)
    {
        $lastRound = $this->getLastRoundForSeason($season);
        return $this->em->getRepository($this::MATCH_ENTITY)->findBy(array('round' => $lastRound, 'season' => $season));
    }
    
    public function getMatchesForSeason($season)
    {
        return $this->em->getRepository($this::MATCH_ENTITY)->findBy(array('season' => $season));
    }
    
    public function getHomeMatchesForTeamAndSeason($team,$season)
    {
        return $this->em->getRepository($this::MATCH_ENTITY)->findBy(array('season' => $season, 'home' => $team));
    }


    public function getVisitorMatchesForTeamAndSeason($team,$season)
    {
        return $this->em->getRepository($this::MATCH_ENTITY)->findBy(array('season' => $season, 'visitor' => $team));
    }
}
