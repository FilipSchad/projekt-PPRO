<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI;
use App\Model\SeasonManager;

class ManagementPresenter extends Nette\Application\UI\Presenter
{
    /**
     * @inject
     * @var \Kdyby\Doctrine\EntityManager
     */
    public $EntityManager;

    public function beforeRender()
    {
        parent::beforeRender();
        // Management is available only for logged in users.
        if(!$this->user->isLoggedIn()) {
            $this->flashMessage('Administrace je dostupná pouze po přihlášení', 'error');
            $this->redirect('Homepage:adminLogin');
        }
        
        $seasonMan = new SeasonManager($this->EntityManager);
        $this->template->seasons = $seasonMan->getSeasons();
    }
}