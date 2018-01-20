<?php

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Entita region.
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\Table(name="region")
 */
class Region extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="regionId")
     * @ORM\GeneratedValue
     */
    protected $regionId;

    /**
     * @ORM\Column(type="string", name="regionName")
     */
    protected $regionName;
    
    /**
     * One Region has Many Playgrounds.
     * @ORM\OneToMany(targetEntity="Playground", mappedBy="playground")
     */
    protected $playgrounds;
}
