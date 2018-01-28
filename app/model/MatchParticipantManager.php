<?php

namespace App\Model;

use Nette\Application\UI;
use App\Model\Entities\MatchParticipant;
use App\Model\PlayerManager;

/*
 * MatchParticipant management
 */

class MatchParticipantManager
{
    const MATCHP_ENTITY = '\App\Model\Entities\MatchParticipant';
    
    public function __construct(\Kdyby\Doctrine\EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getMatchParticipants()
    {
        return $this->em->getRepository($this::MATCHP_ENTITY)->findBy(array());
    }
    
    public function getMatchParticipantsByArray($input)
    {
        return $this->em->getRepository($this::MATCHP_ENTITY)->findBy($input);
    }
    
    public function getMatchParticipantById($id)
    {
        return $this->em->find($this::MATCHP_ENTITY, $id);
    }
    
    public function deleteMatchParticipantById($id)
    {
        $matchp = $this->em->find($this::MATCHP_ENTITY, $id);
        $this->em->remove($matchp);
        $this->em->flush();
    }
    
    public function  getShooterStatForSeason($season)
    {
        $ret = array();
        $dql = "SELECT IDENTITY(m.player) AS playerId, SUM(m.goals) AS totalGoals ".
               "FROM \App\Model\Entities\MatchParticipant m " .
               "WHERE m.season = ?1 " .
               "GROUP BY playerId";
        $shooters = $this->em->createQuery($dql)
                ->setParameter(1, $season)
                ->getResult();
        if (!empty($shooters)) {
            $playerMan = new PlayerManager($this->em);
            foreach ($shooters as $shooter) {
                if ($shooter['totalGoals'] > 0) {
                    array_push(
                            $ret,
                            array(
                                'player' => $playerMan->getPlayerById($shooter['playerId']),
                                'goals' => $shooter['totalGoals'])
                    );
               }
            }
            usort($ret,"App\Model\MatchParticipantManager::sortShooters");
        }
        return $ret;
    }
    
    public function getGoalKeeperStatForSeason($season)
    {
        $ret = array();

        $dql = "SELECT IDENTITY(m.player) AS playerId, SUM(m.releasedGoals) AS released, COUNT(m.releasedGoals) as matchCount " .
               "FROM \App\Model\Entities\MatchParticipant m " .
               "WHERE m.season = ?1 AND m.isKeeper = 1 " .
               "GROUP BY playerId";
        $keepers = $this->em->createQuery($dql)
                ->setParameter(1, $season)
                ->getResult();
        if (!empty($keepers)) {
            $playerMan = new PlayerManager($this->em);
            foreach ($keepers as $keeper) {
                array_push(
                    $ret,
                    array(
                        'player' => $playerMan->getPlayerById($keeper['playerId']),
                        'released' => $keeper['released'],
                        'matchCount' => $keeper['matchCount'],
                        'average' => $keeper['released'] / $keeper['matchCount'],
                    )
                );
            }
            usort($ret,"App\Model\MatchParticipantManager::sortKeepers");
        }
        return $ret;
    }
    
    public function addParticipantFromForm($form, $matchId, $partType)
    {
        try {
            $matchp = new MatchParticipant();
            $values = $form->getValues();
            $playerMan = new PlayerManager($this->em);
            $matchMan = new MatchManager($this->em);
            $player = $playerMan->getPlayerById($values['player_id']);
            $match = $matchMan->getMatchById($matchId);
            $matchp->setPlayer($player);
            $matchp->setGoals($values['goals']);
            $matchp->setRedCard($values['redCard']);
            $matchp->setYellowCard($values['yellowCard']);
            $matchp->setIsKeeper($values['isKeeper']);
            if ($values['isKeeper'] && $values['releasedGoals ']) {
                $matchp->setReleasedGoals($values['isKeeper']);
            }
            else {
                $matchp->setReleasedGoals(0);
            }
            $matchp->setMatch($match);
            $matchp->setTeam($player->getTeam());
            $matchp->setSeason($match->getSeason());
            if ($partType == "home") {
                if ($match->getHomeGoals()) {
                    $match->setHomeGoals($match->getHomeGoals() + $values['goals']);
                }
                else {
                    $match->setHomeGoals($values['goals']);
                }
            }
            else {
                if ($match->getVisitorGoals()) {
                    $match->setVisitorGoals($match->getVisitorGoals() + $values['goals']);
                }
                else {
                    $match->setVisitorGoals($values['goals']);
                }
            }
            
            $this->em->persist($matchp);
            $this->em->persist($match);
            $this->em->flush();
            return TRUE;
        } catch (Exception $ex) {
            return FALSE;
        }
    }

    public static function sortShooters($a, $b)
    {
        if ($a['goals'] == $b['goals']) {
            return 0;
        }
        return ($a['goals'] < $b['goals']) ? 1 : -1;
    }
    
    public static function sortKeepers ($a,$b)
    {
        if ($a['average'] == $b['average']) {
            if ($a['matchCount'] == $b['matchCount']) {
            return 0;
        }
        return ($a['matchCount'] < $b['matchCount']) ? 1 : -1;
        }
        return ($a['average'] < $b['average']) ? -1 : 1;
    }
}
