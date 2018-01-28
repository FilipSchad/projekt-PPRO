<?php

namespace App\Model;

use Nette\Application\UI;
use App\Model\Entities\Match;
use App\Model\ArbiterManager;
use App\Model\TeamManager;
use App\Model\SeasonManager;
use App\Model\PlaygroundManager;

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
    
    public function getMatchForm()
    {
        $form = new UI\Form;
        $teamMan = new TeamManager($this->em);
        $teams = $teamMan->getTeams();
        $teamArr = array( "" => "");
        foreach ($teams as $team) {
            $teamArr[$team->getTeamId()] = $team->getTeamName();
        }
        $form->addSelect('home_id', 'Domácí:')
                ->setItems($teamArr)
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Vyber tým');
        $form->addSelect('visitor_id', 'Hosté:')
                ->setItems($teamArr)
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Vyber tým');
        
        $seasonMan = new SeasonManager($this->em);
        $seasons = $seasonMan->getSeasons();
        $seasonsArr = array( "" => "");
        foreach ($seasons as $season) {
            $seasonsArr[$season->getSeasonId()] = $season->getSeasonName();
        }
        $form->addSelect('season_id', 'Sezóna:')
                ->setItems($seasonsArr)
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Vyber sezónu');
        
        $form->addInteger('round', 'Kolo:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Kolo');
        
        $arbiterMan = new ArbiterManager($this->em);
        $arbiters = $arbiterMan->getArbiters();
        $arbiterArr = array( "" => "");
        foreach ($arbiters as $arbiter) {
            $arbiterArr[$arbiter->getArbiterId()] = $arbiter->getSurname() . " " . $arbiter->getName();
        }
        $form->addSelect('arbiter_id', 'Rozhodčí:')
                ->setItems($arbiterArr)
                ->setAttribute('placeholder', 'Vyber rozhodčího');
        
        $playgrMan = new PlaygroundManager($this->em);
        $playgrounds = $playgrMan->getPlaygrounds();
        $playgroundArr = array( "" => "");
        foreach ($playgrounds as $playground) {
            $playgroundArr[$playground->getPlaygroundId()] = $playground->getPlaygroundName();
        }
        $form->addSelect('playground_id', 'Hřiště:')
                ->setItems($playgroundArr)
                ->setAttribute('placeholder', 'Vyber hřiště');
        
        $form->addText('matchdate', 'Datum zápasu:')
                ->setAttribute('placeholder', 'Datum zápasu')
                ->setAttribute('class', 'date_matchdate');
        $form->addCheckbox('played', 'Odehráno');
        return $form;
    }
    
    public function createOrUpdateMatchFromForm($form, $id)
    {
        try {
            if ($id > 0) {
                $match = $this->getMatchById($id);
            }
            else
            {
                $match = new Match();
            }
            $values = $form->getValues();
            $teamMan = new TeamManager($this->em);
            $seasonMan = new SeasonManager($this->em);
            $playgrMan = new PlaygroundManager($this->em);
            $arbiterMan = new ArbiterManager($this->em);
            $match->setHome($teamMan->getTeamById($values['home_id']));
            $match->setVisitor($teamMan->getTeamById($values['visitor_id']));
            $match->setSeason($seasonMan->getSeasonById($values['season_id']));
            $match->setRound($values['round']);
            if ($values['matchdate'] && strtotime($values['matchdate'])) {
                $match->setMatchDate(new \DateTime($values['matchdate']));
            }
            $match->setPlayed($values['played']);
            $match->setPlayground($playgrMan->getPlaygroundById($values['playground_id']));
            $match->setArbiter($arbiterMan->getArbiterById($values['arbiter_id']));
                        
            $this->em->persist($match);
            $this->em->flush();
            return TRUE;
        } catch (Exception $ex) {
            dump($ex);
            return FALSE;
        }
    }
}
