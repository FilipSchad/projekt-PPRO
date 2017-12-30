<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\PlayerManager;
use App\Model\SeasonManager;
use App\Model\TeamManager;
use App\Model\Entities\Team;

class HomepagePresenter extends Nette\Application\UI\Presenter
{
    /**
     * @inject
     * @var \Kdyby\Doctrine\EntityManager
     */
    public $EntityManager;

    public function beforeRender()
    {
        parent::beforeRender();
        $logout = $this->getParameter('logout');
        if ($logout) {
            $this->getUser()->logout();
            $this->flashMessage('Byl jste úspěšně odhlášen.');
            $this->redirect('Homepage:');
        }
        $seasonMan = new SeasonManager($this->EntityManager);
        $this->template->seasons = $seasonMan->getSeasons();
    }
      
    protected function createComponentSignInForm()
    {
        $form = new Form;
        $form->addText('name', 'Jméno:')->setRequired('Zadejte prosím login');
        $form->addPassword('password', 'Heslo:')->setRequired('Zadejte prosím heslo');
        $form->addSubmit('login', 'Přihlásit');
        $form->onValidate[] = [$this, 'validateSignInForm'];
        $form->onSuccess[] = function () {
            $this->flashMessage('Byl jste úspěšně přihlášen.');
            $this->redirect('Homepage:');
        };
        return $form;
    }

    public function validateSignInForm($form)
    {
        $values = $form->getValues();
        $user = $this->getUser();
        try
        {
            $user->login($values['name'], $values['password']);
        }
        catch (Nette\Security\AuthenticationException $e)
        {
            $form->addError('Uživatelské jméno nebo heslo je nesprávné');
        }
    }
    
    protected function createComponentRegisterPlayerForm()
    {
        $playerMan = new PlayerManager($this->EntityManager);
        $form = $playerMan->getPlayerForm();
        $form->addSubmit('login', 'Registrovat')
                ->setAttribute('class', 'submit_button');
        $form->onSuccess[] = [$this, 'registerPlayerFormSucceeded'];
        return $form;
    }
    
    public function registerPlayerFormSucceeded(Form $form, $values)
    {
        try {
            $playerMan = new PlayerManager($this->EntityManager);
            $playerMan->registerPlayer($values);
            $this->flashMessage('Hráč byl úspěšně registrován.');
            $this->redirect('Homepage:');
        }
        catch (\Doctrine\DBAL\DBALException $e) {
            $this->flashMessage('Nepodařilo se zaregistrovat hráče.', 'error');
            
        }
    }
    
    protected function createComponentRegisterTeamForm()
    {
        $teamMan = new TeamManager($this->EntityManager);
        $form = $teamMan->getTeamForm();
        $form->addSubmit('login', 'Registrovat')
                ->setAttribute('class', 'submit_button');
        $form->onSuccess[] = [$this, 'registerTeamFormSucceeded'];
        return $form;
    }
    
    public function registerTeamFormSucceeded(Form $form, $values)
    {
        try {
            $newTeam = new Team;
            $newTeam->setTeamName($values['team_name']);
            $newTeam->setOwnerName($values['owner_name']);
            $newTeam->setOwnerSurname($values['owner_surname']);
            $newTeam->setAddress($values['address']);
            $newTeam->setCity($values['city']);
            $newTeam->setPostcode($values['postcode']);
            $newTeam->setEmail($values['email']);
            $newTeam->setPhone($values['phone']);
            $newTeam->setCode($values['code']);
            $newTeam->setRegistrationDate(new \DateTime("now"));
            $this->EntityManager->persist($newTeam);
            $this->EntityManager->flush();
            $this->flashMessage('Tým byl úspěšně registrován.');
            $this->redirect('Homepage:');
        }
        catch (\Doctrine\DBAL\DBALException $e) {
            $this->flashMessage('Nepodařilo se zaregistrovat tým.', 'error');
        }
    }
}
