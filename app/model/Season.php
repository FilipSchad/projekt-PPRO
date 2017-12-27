<?php

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Doctrine entita pro tabulku season.
 * @package App\Model
 * @ORM\Entity
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
