<?php

namespace App\Model;

use Nette\Application\UI;
use App\Model\Entities\Team;
use App\Model\MatchManager;

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
        return $this->em->getRepository($this::TEAM_ENTITY)->findBy(array(), array('teamName' => 'ASC'));
    }
    
    public function getTeamById($id)
    {
        return $this->em->find($this::TEAM_ENTITY, $id);
    }
    
    public function deleteTeamById($id)
    {
        $team = $this->em->find($this::TEAM_ENTITY, $id);
        if (count($team->getPlayers()) == 0) {
            $this->em->remove($team);
            $this->em->flush();
        }
        else
        {
            throw new \Exception("Není možné smazat tým, který obsahuje hráče.");
        }
    }
    
    public function getTeamForm()
    {
        $form = new UI\Form;
        $form->addText('team_name', 'Jméno týmu:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Jméno týmu');
        $form->addText('owner_name', 'Jméno manžera týmu:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Jméno manažera týmu');
        $form->addText('owner_surname', 'Příjmení manažera týmu:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Příjmení manažera týmu');
        $form->addText('address', 'Ulice a ČP:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Ulice a ČP');
        $form->addText('city', 'Město:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Město');
        $form->addText('postcode', 'PSČ:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'PSČ');
        $form->addText('email', 'Email:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Email');
        $form->addText('phone', 'Telefon:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Telefon');
        $form->addText('code', 'Registrační kód:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Registrační kód');
        return $form;
    }
    
    public function getStatsForTeamAndSeason($team,$season, $matchMan) {
        $stat = array (
            'playedMatches'  => 0,
            'wins'           => 0,
            'loses'          => 0,
            'draws'          => 0,
            'shooted_goals'  => 0,
            'released_goals' => 0,
            'points'         => 0,
            'score'          => 0,
        );
        
        $homeMatches = $matchMan->getHomeMatchesForTeamAndSeason($team,$season);
        foreach ($homeMatches as $match) {
            if ($match->getHomeGoals() > $match->getVisitorGoals()) {
                $stat['wins'] += 1;
                $stat['points'] += 3;
                //$points += 3;
            } elseif ($match->getHomeGoals() == $match->getVisitorGoals()) {
                $stat['draws'] += 1;
                $stat['points'] += 1;
                //$points += 1;
            } else {
                $stat['loses'] += 1;
            }
            $stat['playedMatches'] += 1;
            $stat['shooted_goals'] += $match->getHomeGoals();
            $stat['released_goals'] += $match->getVisitorGoals();
        }
        
        $visitorMatches = $matchMan->getVisitorMatchesForTeamAndSeason($team,$season);
        foreach ($visitorMatches as $match) {
            if ($match->getHomeGoals() < $match->getVisitorGoals()) {
                $stat['wins'] += 1;
                $stat['points'] += 3;
                //$points += 3;
            } elseif ($match->getHomeGoals() == $match->getVisitorGoals()) {
                $stat['draws'] += 1;
                $stat['points'] += 1;
                //$points += 1;
            } else {
                $stat['loses'] += 1;
            }
            $stat['playedMatches'] += 1;
            $stat['shooted_goals'] += $match->getVisitorGoals();
            $stat['released_goals'] += $match->getHomeGoals();
        }
        $stat['score'] = $stat['shooted_goals'] - $stat['released_goals'];
        return $stat;
    }
    
    public function getStatsTable($season)
    {
        $teams = $this->getTeams();
        $matchMan = new MatchManager($this->em);
        $statTable = array();
        foreach ($teams as $team) {
            $teamStat = $this->getStatsForTeamAndSeason($team, $season, $matchMan);
            $teamStat['teamName'] = $team->getTeamName();
            array_push($statTable, $teamStat);
        }
        
        usort($statTable, "App\Model\TeamManager::sortTeamOrder");
        return $statTable;
    }

    public function getAddPlayerForm($team)
    {
        $form = new UI\Form;
        $players = $team->getPlayers();
        $playerArr = array( "" => "");
        foreach ($players as $player) {
            $playerArr[$player->getPlayerId()] = $player->getName() . " " . $player->getSurname();
        }
        $form->addSelect('player_id', 'Hráč:')
                ->setItems($playerArr)
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Vyber hráče');
        $form->addInteger('goals', 'Góly:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Počet vstřelených gólů');
        $form->addCheckbox('redCard', 'Červená karta');
        $form->addCheckbox('yellowCard', 'Žlutá karta');
        $form->addCheckbox('isKeeper', 'Brankář');
        $form->addInteger('releasedGoals', 'Obdržených gólů');
        return $form;
    }
    
    public function getTopFiveForSeason($season)
    {
        return array_slice($this->getStatsTable($season), 0, 5);
    }

    public static function sortTeamOrder($a, $b)
    {
        if ($a['points'] == $b['points']) {
            if ($a['score'] == $b['score']) {
                return 0;
            }
            return ($a['score'] < $b['score']) ? 1 : -1;
        }
        return ($a['points'] < $b['points']) ? 1 : -1;
    }   
}
