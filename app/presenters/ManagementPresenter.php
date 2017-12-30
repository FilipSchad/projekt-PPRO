<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI;
use App\Model\SeasonManager;
use App\Model\PlayerManager;
use App\Model\TeamManager;

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
    
    public function renderPlayer()
    {
        $playerMan = new PlayerManager($this->EntityManager);
        $this->template->players = $playerMan->getPlayers();
    }
    
    public function renderTeam($id)
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
    
    public function createComponentEditTeamForm()
    {
        
        if (!isset($this->template->selectedTeam)) {
            $id = $this->request->getParameter('id');
            $teamMan = new TeamManager($this->EntityManager);
            $selectedTeam = $teamMan->getTeamById($id);
        }
        else {
            $selectedTeam = $this->template->selectedTeam;
        }
        
        $form = new UI\Form;
        $form->addGroup();
        $form->addHidden('team_id', $selectedTeam->getTeamId());
        $form->addText('team_name', 'Jméno týmu:')->setValue($selectedTeam->getTeamName());
        $form->addText('owner_name', 'Jméno majitele:')->setValue($selectedTeam->getOwnerName());
        $form->addText('owner_surname', 'Příjmení majitele:')->setValue($selectedTeam->getOwnerSurname());
        $form->addText('city', 'Město:')->setValue($selectedTeam->getCity());
        $form->addText('address', 'Adresa:')->setValue($selectedTeam->getAddress());
        $form->addText('postcode', 'PSČ:')->setValue($selectedTeam->getPostcode());
        $form->addText('phone', 'Telefon na majitele:')->setValue($selectedTeam->getPhone());
        $form->addText('email', 'Email na majitele:')->setValue($selectedTeam->getEmail());
        $form->addText('reg_date', 'Datum registrace:')->setValue($selectedTeam->getRegistrationDate()->format('Y-m-d'))
                ->setAttribute('readonly');
        $form->addText('reg_code', 'Registrační kód:')->setValue($selectedTeam->getCode())
                ->setAttribute('readonly');
        $form->addSubmit('save_team', 'Uložit')
                ->setAttribute('id', 'saveButton')
                ->setAttribute('style', 'float:left;border:0;width:197px;');
        $form->onValidate[] = [$this, 'validateEditTeamForm'];
        return $form;
    }
    
    public function validateEditTeamForm($form)
    {
        $values = $form->getValues();
    }
}
