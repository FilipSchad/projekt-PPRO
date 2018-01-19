<?php

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Doctrine entita pro tabulku player.
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\Table(name="player")
 */
class Player extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="playerId")
     * @ORM\GeneratedValue
     */
    protected $playerId;
    
    /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="players")
     * @ORM\JoinColumn(name="teamId", referencedColumnName="teamId")
     */
    protected $team;
    
    /**
     * @ORM\Column(type="datetime", name="registrationDate")
     */
    protected $registrationDate;
    
    /**
     * @ORM\Column(type="string", name="name")
     */
    protected $name;
    
    /**
     * @ORM\Column(type="string", name="surname")
     */
    protected $surname;
    
    /**
     * @ORM\Column(type="datetime", name="birthDate")
     */
    protected $birthDate;
    
    /**
     * @ORM\Column(type="string", name="phone")
     */
    protected $phone;
    
    /**
     * @ORM\Column(type="string", name="email")
     */
    protected $email;
    
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
     * @ORM\Column(type="string", name="photo")
     */
    protected $photo = 'photos/players/no_photo_available.jpg';
}
