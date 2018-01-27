<?php

namespace App\Presenters;

use Nette;
use App\Model\SeasonManager;
use App\Model\PlaygroundManager;
use App\Model\RegionManager;

class PlaygroundPresenter extends Nette\Application\UI\Presenter
{
    /**
     * @inject
     * @var \Kdyby\Doctrine\EntityManager
     */
    public $EntityManager;

    public function beforeRender()
    {
        parent::beforeRender();
        $seasonMan = new SeasonManager($this->EntityManager);
        $this->template->seasons = $seasonMan->getSeasons();
    }
    
    
    public function renderDefault($id)
    {
        $playgrMan = new PlaygroundManager($this->EntityManager);
        $this->template->playgrounds = $playgrMan->getPlaygrounds();
        if($id) {
            $this->template->updatePlayground = $playgrMan->getPlaygroundById($id);
        }
        else {
            $this->template->updatePlayground = NULL;
        }
    }
    
    public function handleCreatePlayground()
    {
        $this->template->createPlayground = TRUE;
    }
    
    public function handleDeletePlayground($id)
    {
        $playgrMan = new PlaygroundManager($this->EntityManager);
        try {
            $playgrMan->deletePlaygroundById($id);
            $this->flashMessage('Hřiště bylo úspěšně smazáno.');
            $this->redirect('Playground:');
        }
        catch (\Doctrine\DBAL\DBALException $e) {
            $this->flashMessage('Nepodařilo se smazat hřiště.', 'error');
            $this->redirect('Playground:');
        }
    }
    
    public function createComponentEditPlaygroundForm()
    {
        $id = $this->request->getParameter('id');
        $playgrMan = new PlaygroundManager($this->EntityManager);
        $selectedPlaygr = $playgrMan->getPlaygroundById($id);
        
        $form = $playgrMan->getPlaygroundForm();    
        $form->addSubmit('save_playground', 'Uložit')
                ->setAttribute('id', 'saveButton')
                ->setAttribute('style', 'float:left;border:0;width:197px;');
        $form->getComponent('playground_name')->setValue($selectedPlaygr->getPlaygroundName());
        $form->getComponent('manager_name')->setValue($selectedPlaygr->getManagerName());
        $form->getComponent('manager_surname')->setValue($selectedPlaygr->getManagerSurname());
        $form->getComponent('phone')->setValue($selectedPlaygr->getManagerPhone());
        $form->getComponent('email')->setValue($selectedPlaygr->getManagerEmail());
        $form->getComponent('address')->setValue($selectedPlaygr->getAddress());
        $form->getComponent('city')->setValue($selectedPlaygr->getCity());
        $form->getComponent('postcode')->setValue($selectedPlaygr->getPostcode());
        $form->getComponent('region_id')->setValue($selectedPlaygr->getRegion()->getRegionId());
        $form->onSuccess[] = [$this, 'editPlaygroundFormSucceeded'];
        return $form;
    }
    
    public function editPlaygroundFormSucceeded($form)
    {
        $id = $this->request->getParameter('id');
        $playgrMan = new PlaygroundManager($this->EntityManager);
        $updatedPlaygr = $playgrMan->getPlaygroundById($id);
        try {
            $values = $form->getValues();
            $updatedPlaygr->setPlaygroundName($values['playground_name']);
            $updatedPlaygr->setManagerName($values['manager_name']);
            $updatedPlaygr->setManagerSurname($values['manager_surname']);
            $updatedPlaygr->setManagerPhone($values['phone']);
            $updatedPlaygr->setManagerEmail($values['email']); 
            $updatedPlaygr->setAddress($values['address']);
            $updatedPlaygr->setCity($values['city']);
            $updatedPlaygr->setPostcode($values['postcode']);
            if ($values['region_id'] != $updatedPlaygr->getRegion()->getRegionId()){
                $regionMan = new RegionManager($this->EntityManager);
                $updatedPlaygr->setRegion($regionMan->getRegionById($values['region_id']));
            }            
            $this->EntityManager->persist($updatedPlaygr);
            $this->EntityManager->flush();
            $this->flashMessage('Hřiště bylo úspěšně updatováno.', 'info');  
            $this->redirect('Playground:');
        } catch (Exception $ex) {
            $this->flashMessage('Nepodařilo se updatovat hřiště.', 'error');
            $this->redirect('Playground:');
        }
    }
    
    public function createComponentNewPlaygroundForm()
    {
        $playgrMan = new PlaygroundManager($this->EntityManager);
        $form = $playgrMan->getPlaygroundForm();    
        $form->addSubmit('save_playground', 'Vytvořit')
                ->setAttribute('id', 'saveButton')
                ->setAttribute('style', 'float:left;border:0;width:197px;');
        $form->onSuccess[] = [$this, 'createPlaygroundFormSucceeded'];
        return $form;
    }
    
    public function createPlaygroundFormSucceeded($form)
    {
        $playgrMan = new PlaygroundManager($this->EntityManager);
        if ($playgrMan->createOrUpdatePlaygroundFromForm($form, 0)) {
            $this->flashMessage('Hřiště bylo úspěšně vytvořeno.', 'info');  
            $this->redirect('Playground:');
        }
        else {
            $this->flashMessage('Nepodařilo se vytvořit hřiště.', 'error');
            $this->redirect('Playground:');
        }
    }
}
