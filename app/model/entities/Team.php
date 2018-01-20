<?php

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Entita team.
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\Table(name="team")
 */
class Team extends BaseEntity
{
       /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="teamId")
     * @ORM\GeneratedValue
     */
    protected $teamId;
    
    /**
     * @ORM\Column(type="string", name="teamName")
     */
    protected $teamName;
    
    /**
     * @ORM\Column(type="string", name="ownerName")
     */
    protected $ownerName;
    
    /**
     * @ORM\Column(type="string", name="ownerSurname")
     */
    protected $ownerSurname;
    
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
     * @ORM\Column(type="string", name="phone")
     */
    protected $phone;
    
    /**
     * @ORM\Column(type="string", name="email")
     */
    protected $email;
    
    /**
     * @ORM\Column(type="datetime", name="registrationDate")
     */
    protected $registrationDate;
    
    /**
     * @ORM\Column(type="string", name="code")
     */
    protected $code;
    
    /**
     * @ORM\Column(type="string", name="logo")
     */
    protected $logo = 'photos/teams/no_photo_available.jpg';
    
    /**
     * One Team has Many Players.
     * @ORM\OneToMany(targetEntity="Player", mappedBy="team")
     */
    protected $players;
}
