<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI;
use App\Model\SeasonManager;
use App\Model\PlayerManager;
use App\Model\TeamManager;
use App\Model\PaymentManager;
use App\Model\ArbiterManager;
use App\Model\MatchManager;
use App\Model\MatchParticipantManager;

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
    
    public function renderSeason()
    {
    }
    
    public function createComponentEditPlayerForm()
    {
        $id = $this->request->getParameter('id');
        $playerMan = new PlayerManager($this->EntityManager);
        $selectedPlayer = $playerMan->getPlayerById($id);
        
        $form = $playerMan->getPlayerForm();
        $form->addText('registration_date', 'Datum registrace:')
                ->setValue($selectedPlayer->getRegistrationDate()->format('d. m. Y'))
                ->setAttribute('readonly');
        $form->addSubmit('save_team', 'Uložit')
                ->setAttribute('id', 'saveButton')
                ->setAttribute('style', 'float:left;border:0;width:197px;');
        $form->onSuccess[] = [$this, 'editPlayerFormSucceeded'];
        $form->getComponent('name')->setValue($selectedPlayer->getName());
        $form->getComponent('surname')->setValue($selectedPlayer->getSurname());
        $form->getComponent('birthdate')->setValue($selectedPlayer->getBirthDate()->format('d. m. Y'));
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
    
    public function handleDeleteArbiter($id)
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
    
    public function handleCreateArbiter()
    {
        $this->template->createArbiter = TRUE;
    }
    
    public function createComponentNewArbiterForm()
    {
        $arbiterMan = new ArbiterManager($this->EntityManager);
        $form = $arbiterMan->getArbiterForm();
        $form->addSubmit('create_arbiter', 'Vytvořit')
                ->setAttribute('id', 'saveButton')
                ->setAttribute('style', 'float:left;border:0;width:197px;');
        $form->onSuccess[] = [$this, 'createArbiterFormSucceeded'];
        return $form;
    }

    public function createComponentEditArbiterForm()
    {
        $id = $this->request->getParameter('id');
        $arbiterMan = new ArbiterManager($this->EntityManager);
        $selectedArbiter = $arbiterMan->getArbiterById($id);
        
        $form = $arbiterMan->getArbiterForm();
        $form->addSubmit('save_arbiter', 'Uložit')
                ->setAttribute('id', 'saveButton')
                ->setAttribute('style', 'float:left;border:0;width:197px;');
        $form->onSuccess[] = [$this, 'editArbiterFormSucceeded'];
        $form->getComponent('name')->setValue($selectedArbiter->getName());
        $form->getComponent('surname')->setValue($selectedArbiter->getSurname());
        $form->getComponent('address')->setValue($selectedArbiter->getAddress());
        $form->getComponent('city')->setValue($selectedArbiter->getCity());
        $form->getComponent('postcode')->setValue($selectedArbiter->getPostcode());
        $form->getComponent('email')->setValue($selectedArbiter->getEmail());
        $form->getComponent('phone')->setValue($selectedArbiter->getPhone());
        return $form;
    }
    
    public function createArbiterFormSucceeded($form)
    {
        $arbiterMan = new ArbiterManager($this->EntityManager);
        if($arbiterMan->createOrUpdateArbiterFromForm($form, 0)) {
            $this->flashMessage('Rozhodčí byl úspěšně vytvořen.', 'info');  
            $this->redirect('Management:arbiter');
        }
        else {
            $this->flashMessage('Nepodařilo se vytvořit rozhodčí.', 'error');
            $this->redirect('Management:arbiter');
        }
    }
    
    public function editArbiterFormSucceeded($form)
    {
        $id = $this->request->getParameter('id');
        $arbiterMan = new ArbiterManager($this->EntityManager);
        if($arbiterMan->createOrUpdateArbiterFromForm($form, $id)) {
            $this->flashMessage('Rozhodčí byl úspěšně updatován.', 'info');  
            $this->redirect('Management:arbiter');
        }
        else {
            $this->flashMessage('Nepodařilo se updatovat rozhodčího.', 'error');
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
 
    public function handleCreatePayment()
    {
        $this->template->createPayment = TRUE;
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
    
    public function handleSetActiveSeason($id)
    {
        $seasonMan = new SeasonManager($this->EntityManager);
        $seasonMan->setActualSeason($seasonMan->getSeasonById($id));
        $this->flashMessage('Nastavena aktivní sezóna ' . $seasonMan->getActualSeason()->getSeasonName(), 'info');
        $this->redirect('Management:season');
    }
    
    public function handleDeleteSeason($id)
    {
        $seasMan = new SeasonManager($this->EntityManager);
        try {
            $seasMan->deleteSeasonById($id);
            $this->flashMessage('Sezóna byla úspěšně smazána.');
            $this->redirect('Management:season');
        }
        catch (\Doctrine\DBAL\DBALException $e) {
            $this->flashMessage('Nepodařilo se smazat sezónu.', 'error');
            $this->redirect('Management:season');
        }
    }
    
    public function handleCreateSeason()
    {        
        $this->template->showNewSeasonForm = TRUE;
    }
    
    public function createComponentNewSeasonForm()
    {
        $seasonMan = new SeasonManager($this->EntityManager);
        $form = $seasonMan->getSeasonForm();
        
        $form->addSubmit('create_season', 'Vytvořit')
                ->setAttribute('id', 'saveButton')
                ->setAttribute('style', 'float:left;border:0;width:197px;');
        $form->onSuccess[] = [$this, 'createSeasonFormSucceeded'];
        return $form;
    }
    
    public function createSeasonFormSucceeded($form)
    {
        $seasMan = new SeasonManager($this->EntityManager);
        $seasonName = $form->getValues()['name'];
        try {
            $ns = new \App\Model\Entities\Season();
            $ns->setSeasonName($seasonName);
            $ns->setActual(0);
            $this->EntityManager->persist($ns);
            $this->EntityManager->flush();
            $this->flashMessage('Sezńa byla úspěšně vytvořena.', 'info');  
            $this->redirect('Management:season');
        }
        catch (Exception $ex) {
            $this->flashMessage('Nepodařilo se vytvořit sezónu.', 'error');
            $this->redirect('Management:season');
        }
    }
    
    public function renderMatch($id)
    {
        $matchMan = new MatchManager($this->EntityManager);
        $this->template->matches = $matchMan->getMatches();
        
        if ($id)
        {
            $this->template->selectedMatch = $matchMan->getMatchById($id);
            $matchpMan = new MatchParticipantManager($this->EntityManager);
            //Get home players for selected match
            $this->template->selHomePlayers = $matchpMan->getMatchParticipantsByArray(
                    array(
                        'match' => $this->template->selectedMatch,
                        'team'  => $this->template->selectedMatch->getHome(),
                        'season'  => $this->template->selectedMatch->getSeason()
                    ));
            //Get visitor players for selected match
            $this->template->selVisitorPlayers = $matchpMan->getMatchParticipantsByArray(
                    array(
                        'match' => $this->template->selectedMatch,
                        'team'  => $this->template->selectedMatch->getVisitor(),
                        'season'  => $this->template->selectedMatch->getSeason()
                    ));
        }
        else
        {
            $this->template->selectedMatch = NULL;
        }
    }
    
    public function handleCreateMatch()
    {
        $this->template->createMatch = TRUE;
    }
    
    public function createComponentNewMatchForm()
    {
        $matchMan = new MatchManager($this->EntityManager);
        $form = $matchMan->getMatchForm();
        $form->addSubmit('create_match', 'Vytvořit')
                ->setAttribute('id', 'saveButton')
                ->setAttribute('style', 'float:left;border:0;width:197px;');
        $form->onSuccess[] = [$this, 'createMatchFormSucceeded'];
        return $form;
    }
    
    public function createMatchFormSucceeded($form)
    {
        $matchMan = new MatchManager($this->EntityManager);
        if($matchMan->createOrUpdateMatchFromForm($form, 0)) {
            $this->flashMessage('Zápas byl úspěšně vytvořen.', 'info');  
            $this->redirect('Management:match');
        }
        else {
            $this->flashMessage('Nepodařilo se vytvořit zápas.', 'error');
            $this->redirect('Management:match');
        }
    }
    
    public function editMatchFormSucceeded($form)
    {
        $id = $this->request->getParameter('id');
        $matchMan = new MatchManager($this->EntityManager);
        if($matchMan->createOrUpdateMatchFromForm($form, $id)) {
            $this->flashMessage('Zápas byl úspěšně updatován.', 'info');  
            $this->redirect('Management:match');
        }
        else {
            $this->flashMessage('Nepodařilo se updatovat zápas.', 'error');
            $this->redirect('Management:match');
        }
    }
    
    public function createComponentEditMatchForm()
    {
        $id = $this->request->getParameter('id');
        $matchMan = new MatchManager($this->EntityManager);
        $selectedMatch = $matchMan->getMatchById($id);
        
        $form = $matchMan->getMatchForm();
        $form->addSubmit('save_match', 'Uložit')
                ->setAttribute('id', 'saveButton')
                ->setAttribute('style', 'float:left;border:0;width:197px;');
        $form->getComponent('home_id')->setValue($selectedMatch->getHome()->getTeamId());
        $form->getComponent('visitor_id')->setValue($selectedMatch->getVisitor()->getTeamId());
        $form->getComponent('season_id')->setValue($selectedMatch->getSeason()->getSeasonId());
        $form->getComponent('round')->setValue($selectedMatch->getRound());
        $form->getComponent('arbiter_id')->setValue($selectedMatch->getArbiter()->getArbiterId());
        $form->getComponent('playground_id')->setValue($selectedMatch->getPlayground()->getPlaygroundId());
        $form->getComponent('matchdate')->setValue($selectedMatch->getMatchDate()->format('d. m. Y'));
        $form->getComponent('played')->setValue($selectedMatch->getPlayed());
        $form->onSuccess[] = [$this, 'editMatchFormSucceeded'];
        return $form;
    }
    
    public function createComponentAddHomePlayerForm()
    {
        
        $id = $this->request->getParameter('id');
        $matchMan = new MatchManager($this->EntityManager);
        $selectedMatch = $matchMan->getMatchById($id);
        
        $teamMan = new TeamManager($this->EntityManager);
        $form = $teamMan->getAddPlayerForm($selectedMatch->getHome());
        $form->addSubmit('add_home_player', 'Přidat hráče')
                ->setAttribute('id', 'saveButton')
                ->setAttribute('style', 'float:left;border:0;width:197px;');
        $form->onSuccess[] = [$this, 'addHomePlayerFormSucceeded'];
        return $form;
    }
    
    public function addHomePlayerFormSucceeded($form)
    {
        $id = $this->request->getParameter('id');
        $matchpMan = new MatchParticipantManager($this->EntityManager);
        if($matchpMan->addParticipantFromForm($form, $id, "home")) {
            $this->flashMessage('Hráč byl úspěšně přidán do zápasu.', 'info');  
            $this->redirect('Management:match', $id);
        }
        else {
            $this->flashMessage('Nepodařilo se přidat hráče do zápasu.', 'error');
            $this->redirect('Management:match', $id);
        }
    }

    public function addVisitorPlayerFormSucceeded($form)
    {
        $id = $this->request->getParameter('id');
        $matchpMan = new MatchParticipantManager($this->EntityManager);
        if($matchpMan->addParticipantFromForm($form, $id, "visitor")) {
            $this->flashMessage('Hráč byl úspěšně přidán do zápasu.', 'info');  
            $this->redirect('Management:match', $id);
        }
        else {
            $this->flashMessage('Nepodařilo se přidat hráče do zápasu.', 'error');
            $this->redirect('Management:match', $id);
        }
    }
    
    public function createComponentAddVisitorPlayerForm()
    {
        
        $id = $this->request->getParameter('id');
        $matchMan = new MatchManager($this->EntityManager);
        $selectedMatch = $matchMan->getMatchById($id);
        
        $teamMan = new TeamManager($this->EntityManager);
        $form = $teamMan->getAddPlayerForm($selectedMatch->getVisitor());
        $form->addSubmit('add_visitor_player', 'Přidat hráče')
                ->setAttribute('id', 'saveButton')
                ->setAttribute('style', 'float:left;border:0;width:197px;');
        $form->onSuccess[] = [$this, 'addVisitorPlayerFormSucceeded'];
        return $form;
    }
    
    public function handleDeleteMatch($id)
    {
        $matchMan = new MatchManager($this->EntityManager);
        try {
            $matchMan->deleteMatchById($id);
            $this->flashMessage('Zápas byl úspěšně smazán.');
            $this->redirect('Management:match');
        }
        catch (\Doctrine\DBAL\DBALException $e) {
            $this->flashMessage('Nepodařilo se smazat zápas.', 'error');
            $this->redirect('Management:match');
        }
        catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
            $this->redirect('Management:match');
        }
    }
    
    public function handleDeleteHomePlayer($id)
    {
        $matchpMan = new MatchParticipantManager($this->EntityManager);
        $matchp = $matchpMan->getMatchParticipantById($id);
        $match = $matchp->getMatch();
        try {
            if ($matchp->getGoals()) {
                $match->setHomeGoals($match->getHomeGoals() - $matchp->getGoals());
            }
            $matchpMan->deleteMatchParticipantById($id);
            $this->flashMessage('Hráč byl úspěšně odebrán ze zápasu');
            $this->redirect('Management:match', $match->getMatchId());
        }
        catch (\Doctrine\DBAL\DBALException $e) {
            $this->flashMessage('Nepodařilo se odebrat hráče.', 'error');
            $this->redirect('Management:match', $match->getMatchId());
        }
    }
    
    public function handleDeleteVisitorPlayer($id)
    {
        $matchpMan = new MatchParticipantManager($this->EntityManager);
        $matchp = $matchpMan->getMatchParticipantById($id);
        $match = $matchp->getMatch();
        try {
            if ($matchp->getGoals()) {
                $match->setVisitorGoals($match->getVisitorGoals() - $matchp->getGoals());
            }
            $matchpMan->deleteMatchParticipantById($id);
            $this->flashMessage('Hráč byl úspěšně odebrán ze zápasu');
            $this->redirect('Management:match', $match->getMatchId());
        }
        catch (\Doctrine\DBAL\DBALException $e) {
            $this->flashMessage('Nepodařilo se odebrat hráče.', 'error');
            $this->redirect('Management:match', $match->getMatchId());
        }
    }
}
