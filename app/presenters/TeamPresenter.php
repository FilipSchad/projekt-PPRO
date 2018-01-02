<?php

namespace App\Presenters;

use Nette;
use App\Model\SeasonManager;
use App\Model\TeamManager;
use App\Model\PlayerManager;

class TeamPresenter extends Nette\Application\UI\Presenter
{
    /**
     * @inject
     * @var \Kdyby\Doctrine\EntityManager
     */
    public $EntityManager;
    
    public function beforeRender()
    {
        parent::beforeRender();
        $seasonMan = new SeasonManager($this->EntityManager);
        $this->template->seasons = $seasonMan->getSeasons();
    }
    
    public function renderDefault($id)
    {
        $teamMan = new TeamManager($this->EntityManager);
        $this->template->teams = $teamMan->getTeams();
        if ($id)
        {
            $this->template->selectedTeam = $teamMan->getTeamById($id);
            $playerMan = new PlayerManager($this->EntityManager);
            $this->template->players = $playerMan->getPlayersByTeamId($id);
        }
        else
        {
            $this->template->selectedTeam = NULL;
        }
    }
}