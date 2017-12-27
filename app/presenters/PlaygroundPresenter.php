<?php

namespace App\Presenters;

use Nette;


class PlaygroundPresenter extends Nette\Application\UI\Presenter
{
    /**
     * @inject
     * @var \Kdyby\Doctrine\EntityManager
     */
    public $EntityManager;

    public function renderDefault()
    {
        $dao = $this->EntityManager->getRepository(\App\Model\Season::getClassName());
        $this->template->seasons = $dao->findAll();
    }
}
