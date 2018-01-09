<?php

namespace App\Presenters;

use Nette;
use App\Model\SeasonManager;
use App\Model\TeamManager;

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
        }
        else
        {
            $this->template->selectedTeam = NULL;
        }
    }
}
