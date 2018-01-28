<?php

namespace App\Model;

use Nette\Application\UI;
use App\Model\Entities\Arbiter;

/*
 * Arbiter management
 */

class ArbiterManager
{
    const ARBITER_ENTITY = '\App\Model\Entities\Arbiter';
    
    public function __construct(\Kdyby\Doctrine\EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getArbiters()
    {
        return $this->em->getRepository($this::ARBITER_ENTITY)->findBy(array());
    }
    
    public function getArbiterById($id)
    {
        return $this->em->find($this::ARBITER_ENTITY, $id);
    }
    
    public function deleteArbiterById($id)
    {
        $arbiter = $this->em->find($this::ARBITER_ENTITY, $id);
        $this->em->remove($arbiter);
        $this->em->flush();
    }
    
    public function getArbiterForm()
    {
        $form = new UI\Form;
        $form->addText('name', 'Jméno:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Jméno');
        $form->addText('surname', 'Příjmení:')
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
        return $form;
    }
    
    public function createOrUpdateArbiterFromForm($form, $id)
    {
        try {
            if ($id > 0) {
                $arbiter = $this->getArbiterById($id);
            }
            else
            {
                $arbiter = new Arbiter();
            }
            $values = $form->getValues();
            $arbiter->setName($values['name']);
            $arbiter->setSurname($values['surname']);
            $arbiter->setPhone($values['phone']);
            $arbiter->setEmail($values['email']); 
            $arbiter->setAddress($values['address']);
            $arbiter->setCity($values['city']);
            $arbiter->setPostcode($values['postcode']);          
            $this->em->persist($arbiter);
            $this->em->flush();
            return TRUE;
        } catch (Exception $ex) {
            dump($ex);
            return FALSE;
        }
    }
}
