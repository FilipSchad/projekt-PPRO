<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI;
use Nette\Security\User;
use App\Model\SeasonManager;

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
        $form = new UI\Form;
        $form->addText('name', 'Jméno:')->setRequired('Zadejte prosím login');
        $form->addPassword('password', 'Heslo:')->setRequired('Zadejte prosím heslo');
        $form->addSubmit('login', 'Přihlásit');
        $form->onValidate[] = [$this, 'validateSignInForm'];
        $form->onSuccess[] = function (UI\Form $form, \stdClass $values) {
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
    
    protected function createComponentRegistrationForm()
    {
        $form = new UI\Form;
        $form->addText('name', 'Jméno:');
        $form->addPassword('password', 'Heslo:');
        $form->addSubmit('login', 'Registrovat');
        $form->onSuccess[] = [$this, 'registrationFormSucceeded'];
        return $form;
    }
    
    // volá se po úspěšném odeslání formuláře
    public function registrationFormSucceeded(UI\Form $form, $values)
    {
        // ...
        $this->flashMessage('Byl jste úspěšně registrován.');
        $this->redirect('Homepage:');
    }
}
