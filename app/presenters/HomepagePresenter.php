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

    public function renderDefault()
    {
        $seasonMan = new SeasonManager($this->EntityManager);
        $this->template->seasons = $seasonMan->getSeasons();
        $user = $this->getUser();
        $user->logout();
        try
        {
            $user->login('admin', 'adminaaaa');
        }
        catch (Nette\Security\AuthenticationException $e)
        {
             $this->flashMessage('Uživatelské jméno nebo heslo je nesprávné', 'warning');
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
