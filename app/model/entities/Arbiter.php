<?php

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Entita arbiter.
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\Table(name="arbiter")
 */
class Arbiter extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="arbiterId")
     * @ORM\GeneratedValue
     */
    protected $arbiterId;
    
    /**
     * @ORM\Column(type="string", name="name")
     */
    protected $name;
    
    /**
     * @ORM\Column(type="string", name="surname")
     */
    protected $surname;
    
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
}
