<?php

namespace App\Model;

use Nette\Application\UI;
use App\Model\Entities\Season;

/*
 * Season management
 */

class SeasonManager
{
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
    
    public function getSeasonForm()
    {
        $form = new UI\Form;
        $form->addText('name', 'Název sezóny:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Název');
        return $form;
    }
}
