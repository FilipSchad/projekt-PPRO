<?php

namespace App\Presenters;

use Nette;
use App\Model\SeasonManager;

class RulesPresenter extends Nette\Application\UI\Presenter
{
    /**
     * @inject
     * @var \Kdyby\Doctrine\EntityManager
     */
    public $EntityManager;
    
    public function renderDefault()
    {
        $seasonMan = new SeasonManager($this->EntityManager);
        $this->template->seasons = $seasonMan->getSeasons();
    }
}
