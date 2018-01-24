<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI;
use App\Model\SeasonManager;
use App\Model\MatchManager;

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
        $this->template->reqSeason = $this->template->seasonMan->getSeasonById($id);
        $matchMan = new MatchManager($this->EntityManager);
        if(!isset($this->template->showAll)) {
            $this->template->lastRound = $matchMan->getLastRoundForSeason($this->template->reqSeason);
            $this->template->matches = $matchMan->getLastRoundMatchesForSeason($this->template->reqSeason);
        }
        else {
            $this->template->matches = $matchMan->getMatchesForSeason($this->template->reqSeason);
        }
    }
    
    public function renderTables($id)
    {
        $this->template->reqSeason = $this->template->seasonMan->getSeasonById($id);
    }
    
    public function renderSchedule($id)
    {
        $this->template->reqSeason = $this->template->seasonMan->getSeasonById($id);
    }
    
    public function handleShowAllMatches()
    {
        $this->template->showAll = TRUE;
    }
}
