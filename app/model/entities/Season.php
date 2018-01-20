<?php

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Entita season.
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\Table(name="season")
 */
class Season extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="seasonId")
     * @ORM\GeneratedValue
     */
    protected $seasonId;

    /**
     * @ORM\Column(type="string", name="seasonName")
     */
    protected $seasonName;
    
    /**
     * @ORM\Column(type="boolean", name="actual")
     */
    protected $actual;
}
