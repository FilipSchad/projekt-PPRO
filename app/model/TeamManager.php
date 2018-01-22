<?php

namespace App\Model;

use Nette\Application\UI;
use App\Model\Entities\Team;

/*
 * Player management
 */

class TeamManager
{
    const TEAM_ENTITY = '\App\Model\Entities\Team';
    
    public function __construct(\Kdyby\Doctrine\EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getTeams()
    {
        return $this->em->getRepository($this::TEAM_ENTITY)->findBy(array(), array('teamName' => 'ASC'));
    }
    
    public function getTeamById($id)
    {
        return $this->em->find($this::TEAM_ENTITY, $id);
    }
    
    public function deleteTeamById($id)
    {
        $team = $this->em->find($this::TEAM_ENTITY, $id);
        if (count($team->getPlayers()) == 0) {
            $this->em->remove($team);
            $this->em->flush();
        }
        else
        {
            throw new \Exception("Není možné smazat tým, který obsahuje hráče.");
        }
    }
    
    public function getTeamForm()
    {
        $form = new UI\Form;
        $form->addText('team_name', 'Jméno týmu:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Jméno týmu');
        $form->addText('owner_name', 'Jméno manžera týmu:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Jméno manažera týmu');
        $form->addText('owner_surname', 'Příjmení manažera týmu:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Příjmení manažera týmu');
        $form->addText('address', 'Ulice a ČP:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Ulice a ČP');
        $form->addText('city', 'Město:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Město');
        $form->addText('postcode', 'PSČ:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'PSČ');
        $form->addText('email', 'Email:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Email');
        $form->addText('phone', 'Telefon:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Telefon');
        $form->addText('code', 'Registrační kód:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Registrační kód');
        return $form;
    }
}
