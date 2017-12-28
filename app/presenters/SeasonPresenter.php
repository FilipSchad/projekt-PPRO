<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI;
use App\Model\SeasonManager;

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
    }
    
    public function renderTables($id)
    {
        $this->template->reqSeason = $this->template->seasonMan->getSeasonById($id);
    }
    
    public function renderSchedule($id)
    {
        $this->template->reqSeason = $this->template->seasonMan->getSeasonById($id);
    }
}
