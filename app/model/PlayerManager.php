<?php

namespace App\Model;

use Nette\Application\UI;
use App\Model\Entities\Player;
use App\Model\TeamManager;

/*
 * Player management
 */

class PlayerManager
{
    const PLAYER_ENTITY = '\App\Model\Entities\Player';
    
    public function __construct(\Kdyby\Doctrine\EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getPlayers()
    {
        return $this->em->getRepository($this::PLAYER_ENTITY)->findBy(array(), array('surname' => 'ASC', 'name' => 'ASC'));
    }
    
    public function getPlayerById($id)
    {
        return $this->em->find($this::PLAYER_ENTITY, $id);
    }
    
    public function deletePlayerById($id)
    {
        $player = $this->em->find($this::PLAYER_ENTITY, $id);
        $this->em->remove($player);
        $this->em->flush();
    }
    
    public function getPlayersByTeamId($id)
    {
        return $this->em->getRepository($this::PLAYER_ENTITY)
                ->findBy(
                        array('teamId' => $id), 
                        array('surname' => 'ASC', 'name' => 'ASC')
                );
    }
    
    public function registerPlayer($values)
    {
        $newPlayer = new Player;
        $newPlayer->setTeamId($values['team_id']);
        $newPlayer->setRegistrationDate(new \DateTime("now"));
        $newPlayer->setName($values['name']);
        $newPlayer->setSurname($values['surname']);
        $newPlayer->setBirthDate(new \DateTime($values['birthdate']));
        $newPlayer->setPhone($values['phone']);
        $newPlayer->setEmail($values['email']);
        $newPlayer->setCity($values['city']);
        $newPlayer->setAddress($values['address']);
        $newPlayer->setPostcode($values['postcode']);
        $this->em->persist($newPlayer);
        $this->em->flush();
    }
    
    public function getPlayerForm()
    {
        $form = new UI\Form;
        $form->addText('name', 'Jméno:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Jméno');
        $form->addText('surname', 'Příjmení:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Příjmení');
        $form->addText('birthdate', 'Datum narození:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Datum narození')
                ->setAttribute('class', 'date_birthdate');
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

        $teamMan = new TeamManager($this->em);
        $teams = $teamMan->getTeams();
        $teamArr = array( "" => "");
        foreach ($teams as $team) {
            $teamArr[$team->getTeamId()] = $team->getTeamName();
        }
        $form->addSelect('team_id', 'Tým:')
                ->setItems($teamArr)
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Vyber tým');
        return $form;
    }
}
