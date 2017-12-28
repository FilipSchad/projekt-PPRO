<?php

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Doctrine entita pro tabulku user.
 * @package App\Model\Entitites
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseEntity
{
    
    /** 
     * @ORM\Id
     * @ORM\Column(type="string", name="login")
     */
    protected $login;

    /**
     * @ORM\Column(type="string", name="password")
     */
    protected $password;
}
