<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI;
use App\Model\SeasonManager;
use App\Model\PlayerManager;
use App\Model\TeamManager;
use App\Model\PaymentManager;
use App\Model\ArbiterManager;

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
    
    public function renderPayment($id)
    {
        $payMan = new PaymentManager($this->EntityManager);
        $this->template->payments = $payMan->getPayments();
        if ($id)
        {
            $this->template->selectedPayment = $payMan->getPaymentById($id);
        }
        else
        {
            $this->template->selectedPayment = NULL;
        }
    }
    
    public function renderArbiter($id)
    {
        $arbiterMan = new ArbiterManager($this->EntityManager);
        $this->template->arbiters = $arbiterMan->getArbiters();
        if ($id)
        {
            $this->template->selectedArbiter = $arbiterMan->getArbiterById($id);
        }
        else
        {
            $this->template->selectedArbiter = NULL;
        }
    }
    
    public function renderSeason($id)
    {
        $seasonMan = new SeasonManager($this->EntityManager);
        if ($id)
        {
            $this->template->selectedSeason = $seasonMan->getSeasonById($id);
        }
        else
        {
            $this->template->selectedSeason = NULL;
        }
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
        $form->getComponent('team_id')->setValue($selectedPlayer->getTeam()->getTeamId());
        return $form;
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
            if ($values['team_id'] != $updatedPlayer->getTeam()->getTeamId()){
                $teamMan = new TeamManager($this->EntityManager);
                $updatedPlayer->setTeam($teamMan->getTeamById($values['team_id']));
            }            
            $this->EntityManager->persist($updatedPlayer);
            $this->EntityManager->flush();
            $this->flashMessage('Hráč byl úspěšně updatován.', 'info');  
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
    
    public function handledeleteArbiter($id)
    {
       $arbiterMan = new ArbiterManager($this->EntityManager);
        try {
            $arbiterMan->deleteArbiterById($id);
            $this->flashMessage('Rozhodčí byl úspěšně smazán.');
            $this->redirect('Management:arbiter');
        }
        catch (\Doctrine\DBAL\DBALException $e) {
            $this->flashMessage('Nepodařilo se smazat rozhodčí.', 'error');
            $this->redirect('Management:arbiter');
        }
        catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
            $this->redirect('Management:arbiter');
        } 
    }
    
    public function handledeletePayment($id)
    {
        $paymentMan = new PaymentManager($this->EntityManager);
        try {
            $paymentMan->deletePaymentById($id);
            $this->flashMessage('Platba byla úspěšně smazána.');
            $this->redirect('Management:payment');
        }
        catch (\Doctrine\DBAL\DBALException $e) {
            $this->flashMessage('Nepodařilo se smazat platbu.', 'error');
            $this->redirect('Management:payment');
        }
    }
 
    protected function createComponentNewPaymentForm()
    {
        $payMan = new PaymentManager($this->EntityManager);
        $form = $payMan->getPaymentForm();
        $form->addSubmit('create_payment', 'Vytvořit')
                ->setAttribute('id', 'saveButton')
                ->setAttribute('style', 'float:left;border:0;width:197px;');
        $form->onSuccess[] = [$this, 'createPaymentFormSucceeded'];
        return $form;
    }
    
    public function createPaymentFormSucceeded($form)
    {
        $payMan = new PaymentManager($this->EntityManager);
        if($payMan->createOrUpdatePaymentFromForm($form)) {
            $this->flashMessage('Platba byla úspěšně vytvořena.', 'info');  
            $this->redirect('Management:payment');
        }
        else {
            $this->flashMessage('Nepodařilo se vytvořit platbu.', 'error');
            $this->redirect('Management:payment');
        }
    }
    
    public function createComponentEditPaymentForm()
    {
        $id = $this->request->getParameter('id');
        $payMan = new PaymentManager($this->EntityManager);
        $selectedPayment = $payMan->getPaymentById($id);
        
        $form = $payMan->getPaymentForm();
        $form->getComponent('player_id')->setValue($selectedPayment->getPlayer()->getPlayerId());
        $form->getComponent('purpose')->setValue($selectedPayment->getPurpose());
        $form->getComponent('sum')->setValue($selectedPayment->getSum());
        $form->getComponent('dueDate')->setValue($selectedPayment->getDueDate()->format('d.m.Y'));
        $form->getComponent('payedOn')->setValue($selectedPayment->getPayedOn()->format('d.m.Y'));
        $form->getComponent('variableSymbol')->setValue($selectedPayment->getVariableSymbol());
        $form->getComponent('season_id')->setValue($selectedPayment->getSeason()->getSeasonId());
        
        $teamMan = new teamManager($this->EntityManager);
        $teams = $teamMan->getTeams();
        $teamArr = array( "" => "");
        foreach ($teams as $team) {
            $teamArr[$team->getTeamId()] = $team->getTeamName();
        }
        
        $form->addSelect('team_id', 'Tým:')
                ->setItems($teamArr)
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Vyber tým')
                ->setValue($selectedPayment->getTeam()->getTeamId());
        
        $form->addSubmit('save_payment', 'Uložit')
                ->setAttribute('id', 'saveButton')
                ->setAttribute('style', 'float:left;border:0;width:197px;');
        $form->onSuccess[] = [$this, 'editPaymentFormSucceeded'];
        return $form;
    }
    
    public function editPaymentFormSucceeded($form)
    {
        $id = $this->request->getParameter('id');
        $payMan = new PaymentManager($this->EntityManager);
        if($payMan->createOrUpdatePaymentFromForm($form, $id)) {
            $this->flashMessage('Platba byla úspěšně updatována.', 'info');  
            $this->redirect('Management:payment');
        }
        else {
            $this->flashMessage('Nepodařilo se updatovat platbu.', 'error');
            $this->redirect('Management:payment');
        }
    }
    
    public function handlesetActiveSeason($id)
    {
        
    }
    
    public function handledeleteSeason($id)
    {
        
    }
}
