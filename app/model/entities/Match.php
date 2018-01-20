<?php

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Entita match.
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\Table(name="match")
 */
class Match extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="matchId")
     * @ORM\GeneratedValue
     */
    protected $matchId;
    
    /**
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumn(name="visitorsId", referencedColumnName="teamId")
     */
    protected $visitor;
    
    /**
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumn(name="homeId", referencedColumnName="teamId")
     */
    protected $home;
    
    /**
     * @ORM\Column(type="integer", name="round")
     */
    protected $round;
    
    /**
     * @ORM\Column(type="datetime", name="matchDate")
     */
    protected $matchDate;
    
    /**
     * @ORM\ManyToOne(targetEntity="Season")
     * @ORM\JoinColumn(name="seasonId", referencedColumnName="seasonId")
     */
    protected $season;
    
    /**
     * @ORM\Column(type="integer", name="played")
     */
    protected $played;
    
    /**
     * @ORM\ManyToOne(targetEntity="Playground")
     * @ORM\JoinColumn(name="playgroundId", referencedColumnName="playgroundId")
     */
    protected $playground;
    
    /**
     * @ORM\ManyToOne(targetEntity="Arbiter")
     * @ORM\JoinColumn(name="arbiterId", referencedColumnName="arbiterId")
     */
    protected $arbiter;
    
    /**
     * @ORM\Column(type="integer", name="visitorGoals")
     */
    protected $visitorGoals;
    
    /**
     * @ORM\Column(type="integer", name="homeGoals")
     */
    protected $homeGoals;
}
