<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI;

class HomepagePresenter extends Nette\Application\UI\Presenter
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
