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
    
    public function startup() {
        parent::startup();
        
        // Management is available only for logged in users.
        if(!$this->user->isLoggedIn()) {
            $this->flashMessage('Administrace je dostupná pouze po přihlášení', 'error');
            $this->redirect('Homepage:adminLogin');
        }
    }
    
    public function beforeRender()
    {
        parent::beforeRender();
        $seasonMan = new SeasonManager($this->EntityManager);
        $this->template->seasons = $seasonMan->getSeasons();
    }
    
    public function renderPlayer($id)
    {
        $playerMan = new PlayerManager($this->EntityManager);
        $this->template->players = $playerMan->getPlayers();
        
        if ($id)
        {
            $this->template->selectedPlayer = $playerMan->getPlayerById($id);
        }
        else
        {
            $this->template->selectedPlayer = NULL;
        }
        
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
        $id = $this->request->getParameter('id');
        $teamMan = new TeamManager($this->EntityManager);
        $selectedTeam = $teamMan->getTeamById($id);
        
        $form = $teamMan->getTeamForm();
        $form->addText('registration_date', 'Datum registrace:')
                ->setValue($selectedTeam->getRegistrationDate()->format('Y-m-d'))
                ->setAttribute('readonly');
        $form->addSubmit('save_team', 'Uložit')
                ->setAttribute('id', 'saveButton')
                ->setAttribute('style', 'float:left;border:0;width:197px;');
        $form->onSuccess[] = [$this, 'editTeamFormSucceeded'];
        $form->getComponent('team_name')->setValue($selectedTeam->getTeamName());
        $form->getComponent('owner_name')->setValue($selectedTeam->getOwnerName());
        $form->getComponent('owner_surname')->setValue($selectedTeam->getOwnerSurname());
        $form->getComponent('address')->setValue($selectedTeam->getAddress());
        $form->getComponent('city')->setValue($selectedTeam->getCity());
        $form->getComponent('postcode')->setValue($selectedTeam->getPostcode());
        $form->getComponent('email')->setValue($selectedTeam->getEmail());
        $form->getComponent('phone')->setValue($selectedTeam->getPhone());
        $form->getComponent('code')
                ->setValue($selectedTeam->getCode())
                ->setAttribute('readonly');
        return $form;
    }
    
    public function createComponentEditPlayerForm()
    {
        $id = $this->request->getParameter('id');
        $playerMan = new PlayerManager($this->EntityManager);
        $selectedPlayer = $playerMan->getPlayerById($id);
        
        $form = $playerMan->getPlayerForm();
        $form->addText('registration_date', 'Datum registrace:')
                ->setValue($selectedPlayer->getRegistrationDate()->format('Y-m-d'))
                ->setAttribute('readonly');
        $form->addSubmit('save_team', 'Uložit')
                ->setAttribute('id', 'saveButton')
                ->setAttribute('style', 'float:left;border:0;width:197px;');
        $form->onSuccess[] = [$this, 'editPlayerFormSucceeded'];
        $form->getComponent('name')->setValue($selectedPlayer->getName());
        $form->getComponent('surname')->setValue($selectedPlayer->getSurname());
        $form->getComponent('birthdate')->setValue($selectedPlayer->getBirthDate()->format('Y-m-d'));
        $form->getComponent('address')->setValue($selectedPlayer->getAddress());
        $form->getComponent('city')->setValue($selectedPlayer->getCity());
        $form->getComponent('postcode')->setValue($selectedPlayer->getPostcode());
        $form->getComponent('email')->setValue($selectedPlayer->getEmail());
        $form->getComponent('phone')->setValue($selectedPlayer->getPhone());
        $form->getComponent('team_id')->setValue($selectedPlayer->getTeamId());
        return $form;
    }
    
    public function editTeamFormSucceeded($form)
    {
        $id = $this->request->getParameter('id');
        $teamMan = new TeamManager($this->EntityManager);
        $updatedTeam = $teamMan->getTeamById($id);
        
        try {
            $values = $form->getValues();
            $updatedTeam->setTeamName($values['team_name']);
            $updatedTeam->setOwnerName($values['owner_name']);
            $updatedTeam->setOwnerSurname($values['owner_surname']);
            $updatedTeam->setAddress($values['address']);
            $updatedTeam->setCity($values['city']);
            $updatedTeam->setPostcode($values['postcode']);
            $updatedTeam->setPhone($values['phone']);
            $updatedTeam->setEmail($values['email']);
            $this->EntityManager->persist($updatedTeam);
            $this->EntityManager->flush();
            $this->redirect('Management:team');
        } catch (Exception $ex) {
            $this->flashMessage('Nepodařilo se updatovat tým.', 'error');  
            $this->redirect('Management:team');
        }
        
    }
    
    public function handledeleteTeam($id)
    {
        $teamMan = new TeamManager($this->EntityManager);
        try {
            $teamMan->deleteTeamById($id);
            $this->flashMessage('Tým byl úspěšně smazán.');
            $this->redirect('Management:team');
        }
        catch (\Doctrine\DBAL\DBALException $e) {
            $this->flashMessage('Nepodařilo se smazat tým.', 'error');
            $this->redirect('Management:team');
        }
    }
    
    public function editPlayerFormSucceeded($form)
    {
        $id = $this->request->getParameter('id');
        $playerMan = new PlayerManager($this->EntityManager);
        $updatedPlayer = $playerMan->getPlayerById($id);
        
        try {
            $values = $form->getValues();
            $updatedPlayer->setName($values['name']);
            $updatedPlayer->setSurname($values['surname']);
            $updatedPlayer->setBirthDate(new \DateTime($values['birthdate']));
            $updatedPlayer->setAddress($values['address']);
            $updatedPlayer->setCity($values['city']);
            $updatedPlayer->setPostcode($values['postcode']);
            $updatedPlayer->setEmail($values['email']);
            $updatedPlayer->setPhone($values['phone']);
            $updatedPlayer->setTeamId($values['team_id']);
            $this->EntityManager->persist($updatedPlayer);
            $this->EntityManager->flush();
            $this->redirect('Management:player');
        } catch (Exception $ex) {
            $this->flashMessage('Nepodařilo se updatovat hráče.', 'error');  
            $this->redirect('Management:player');
        }
    }
    
    public function handledeletePlayer($id)
    {
        $playerMan = new PlayerManager($this->EntityManager);
        try {
            $playerMan->deletePlayerById($id);
            $this->flashMessage('Hráč byl úspěšně smazán.');
            $this->redirect('Management:player');
        }
        catch (\Doctrine\DBAL\DBALException $e) {
            $this->flashMessage('Nepodařilo se smazat hráče.', 'error');
            $this->redirect('Management:player');
        }
    }
}
