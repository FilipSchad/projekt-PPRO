<?php

namespace App\Model;

use Nette\Application\UI;
use App\Model\Entities\Season;

/*
 * Season management
 */

class SeasonManager
{
    const SEASON_ENTITY = '\App\Model\Entities\Season';
    
    public function __construct(\Kdyby\Doctrine\EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getSeasons()
    {
        $dao = $this->em->getRepository('\App\Model\Entities\Season');
        return $dao->findAll();
    }
    
    public function getSeasonById($id)
    {
        return $this->em->find('\App\Model\Entities\Season', $id);
    }
    
    public function deleteSeasonById($id)
    {
        $season = $this->em->find($this::SEASON_ENTITY, $id);
        $this->em->remove($season);
        $this->em->flush();
    }
    
    public function getActualSeason()
    {
        return $this->em->getRepository($this::SEASON_ENTITY)->findOneBy(array('actual' => 1));
    }
    
    public function setActualSeason($season)
    {
        $actual = $this->getActualSeason();
        $actual->setActual(0);
        $season->setActual(1);
        $this->em->persist($actual);
        $this->em->persist($season);
        $this->em->flush();
    }


    public function getSeasonForm()
    {
        $form = new UI\Form;
        $form->addText('name', 'Název sezóny:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Název');
        return $form;
    }
}
