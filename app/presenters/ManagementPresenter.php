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
    
    public function renderPlayer()
    {
        $playerMan = new PlayerManager($this->EntityManager);
        $this->template->players = $playerMan->getPlayers();
        
        $teamMan = new TeamManager($this->EntityManager);
        $teams = $teamMan->getTeams();
        $this->template->teamsArr = array();
        foreach ($teams as $t) {
            $this->template->teamsArr[$t->getTeamId()] = $t->getTeamName();
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
            $updatedTeam->setEmail($values['email']);
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
    
    public function handledeletePlayer($id)
    {
        /*$teamMan = new TeamManager($this->EntityManager);
        try {
            $teamMan->deleteTeamById($id);
            $this->flashMessage('Tým byl úspěšně smazán.');
            $this->redirect('Management:team');
        }
        catch (\Doctrine\DBAL\DBALException $e) {
            $this->flashMessage('Nepodařilo se smazat tým.', 'error');
            $this->redirect('Management:team');
        }*/
        $this->flashMessage('Nepodařilo se smazat hráče.', 'error');
        $this->redirect('Management:player');
    }
}
