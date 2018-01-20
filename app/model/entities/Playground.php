<?php

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Entita playground.
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\Table(name="playground")
 */
class Playground extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="playgroundId")
     * @ORM\GeneratedValue
     */
    protected $playgroundId;
    
    /**
     * @ORM\Column(type="string", name="playgroundName")
     */
    protected $playgroundName;
    
    /**
     * @ORM\Column(type="string", name="managerName")
     */
    protected $managerName;
    
    /**
     * @ORM\Column(type="string", name="managerSurname")
     */
    protected $managerSurname;
    
    /**
     * @ORM\Column(type="string", name="managerPhone")
     */
    protected $managerPhone;
    
    /**
     * @ORM\Column(type="string", name="managerEmail")
     */
    protected $managerEmail;
    
    /**
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="playgrounds")
     * @ORM\JoinColumn(name="regionId", referencedColumnName="regionId")
     */
    protected $region;
    
    /**
     * @ORM\Column(type="string", name="city")
     */
    protected $city;
    
    /**
     * @ORM\Column(type="string", name="address")
     */
    protected $address;
    
    /**
     * @ORM\Column(type="string", name="postcode")
     */
    protected $postcode;
    
    /**
     * @ORM\Column(type="string", name="note")
     */
    protected $note;
}
