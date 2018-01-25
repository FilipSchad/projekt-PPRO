<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI;
use App\Model\SeasonManager;
use App\Model\MatchManager;
use App\Model\TeamManager;
use App\Model\MatchParticipantManager;

class SeasonPresenter extends Nette\Application\UI\Presenter
{
    /**
     * @inject
     * @var \Kdyby\Doctrine\EntityManager
     */
    public $EntityManager;

    public function beforeRender()
    {
        parent::beforeRender();
        $this->template->seasonMan = new SeasonManager($this->EntityManager);
        $this->template->seasons = $this->template->seasonMan->getSeasons();
    }
    
    public function renderResults($id)
    {
        $season = $this->template->seasonMan->getSeasonById($id);
        $this->template->reqSeason = $season;
        $matchMan = new MatchManager($this->EntityManager);
        if(!isset($this->template->showAll)) {
            $this->template->lastRound = $matchMan->getLastRoundForSeason($season);
            $this->template->matches = $matchMan->getLastRoundMatchesForSeason($season);
        }
        else {
            $this->template->matches = $matchMan->getMatchesForSeason($season);
        }
    }
    
    public function renderTables($id)
    {
        $season = $this->template->seasonMan->getSeasonById($id);
        $this->template->reqSeason = $season;
        $teamMan = new TeamManager($this->EntityManager);
        $this->template->statsTable = $teamMan->getStatsTable($season);
        
        
        $matchpMan = new MatchParticipantManager($this->EntityManager);
        $this->template->shootersTable = $matchpMan->getShooterStatForSeason($season);
        
        $this->template->goalkeepersTable = $matchpMan->getGoalKeeperStatForSeason($season);;
    }
    
    public function renderSchedule($id)
    {
        $season = $this->template->seasonMan->getSeasonById($id);
        $this->template->reqSeason = $season;
        $matchMan = new MatchManager($this->EntityManager);
        $this->template->matches = $matchMan->getMatchesForSeason($season);
    }
    
    public function handleShowAllMatches()
    {
        $this->template->showAll = TRUE;
    }
}
