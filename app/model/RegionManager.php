<?php

namespace App\Model;
use App\Model\Entities\Region;

/*
 * Region management
 */

class RegionManager
{
    const REGION_ENTITY = '\App\Model\Entities\Region';
    
    public function __construct(\Kdyby\Doctrine\EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getRegions()
    {
        return $this->em->getRepository($this::REGION_ENTITY)->findBy(array());
    }
    
    public function getRegionById($id)
    {
        return $this->em->find($this::REGION_ENTITY, $id);
    }
}
