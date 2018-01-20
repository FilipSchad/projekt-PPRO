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
}
