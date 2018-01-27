<?php

namespace App\Model;

use Nette\Application\UI;
use App\Model\Entities\Playground;
use App\Model\RegionManager;

/*
 * Playground management
 */

class PlaygroundManager
{
    const PLAYGROUND_ENTITY = '\App\Model\Entities\Playground';
    
    public function __construct(\Kdyby\Doctrine\EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getPlaygrounds()
    {
        return $this->em->getRepository($this::PLAYGROUND_ENTITY)->findBy(array(), array('playgroundName' => 'ASC'));
    }
    
    public function getPlaygroundById($id)
    {
        return $this->em->find($this::PLAYGROUND_ENTITY, $id);
    }
    
    public function deletePlaygroundById($id)
    {
        $playground = $this->em->find($this::PLAYGROUND_ENTITY, $id);
        $this->em->remove($playground);
        $this->em->flush();
    }
    
    public function  getPlaygroundForm()
    {
        $form = new UI\Form;
        $form->addText('playground_name', 'Název hřiště:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Jméno');
        $form->addText('manager_name', 'Jméno manažera:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Jméno');
        $form->addText('manager_surname', 'Příjmení manařera:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Příjmení');
        $form->addText('phone', 'Telefon:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Telefon');
        $form->addText('email', 'Email:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Email');
        $form->addText('address', 'Ulice a ČP:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Ulice a ČP');
        $form->addText('city', 'Město:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Město');
        $form->addText('postcode', 'PSČ:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'PSČ');
        
        $regionMan = new RegionManager($this->em);
        $regions = $regionMan->getRegions();
        $regionArr = array( "" => "");
        foreach ($regions as $region) {
            $regionArr[$region->getRegionId()] = $region->getRegionName();
        }
        $form->addSelect('region_id', 'Kraj:')
                ->setItems($regionArr)
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Vyber kraj');
        return $form;
    }
    
    public function createOrUpdatePlaygroundFromForm($form, $id)
    {
        try {
            if ($id > 0) {
                $playground = $this->getPlaygroundById($id);
            }
            else
            {
                $playground = new Playground();
            }
            $values = $form->getValues();
            $playground->setPlaygroundName($values['playground_name']);
            $playground->setManagerName($values['manager_name']);
            $playground->setManagerSurname($values['manager_surname']);
            $playground->setManagerPhone($values['phone']);
            $playground->setManagerEmail($values['email']); 
            $playground->setAddress($values['address']);
            $playground->setCity($values['city']);
            $playground->setPostcode($values['postcode']);
            $regionMan = new RegionManager($this->em);
            $playground->setRegion($regionMan->getRegionById($values['region_id']));
                        
            $this->em->persist($playground);
            $this->em->flush();
            return TRUE;
        } catch (Exception $ex) {
            dump($ex);
            return FALSE;
        }
    }
}
