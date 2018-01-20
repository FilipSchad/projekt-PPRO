<?php

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Entita matchParticipant.
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\Table(name="matchParticipant")
 */
class MatchParticipant extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="matchParticipantId")
     * @ORM\GeneratedValue
     */
    protected $matchParticipantId;
    
    /**
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumn(name="playerId", referencedColumnName="playerId")
     */
    protected $player;
    
    /**
     * @ORM\Column(type="integer", name="goals")
     */
    protected $goals;
    
    /**
     * @ORM\Column(type="boolean", name="redCard")
     */
    protected $redCard;
    
    /**
     * @ORM\Column(type="boolean", name="yellowCard")
     */
    protected $yellowCard;
    
    /**
     * @ORM\Column(type="boolean", name="isKeeper")
     */
    protected $isKeeper;
    
    /**
     * @ORM\Column(type="integer", name="releasedGoals")
     */
    protected $releasedGoals;
    
    /**
     * @ORM\ManyToOne(targetEntity="Match")
     * @ORM\JoinColumn(name="matchId", referencedColumnName="matchId")
     */
    protected $match;
    
    /**
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumn(name="teamId", referencedColumnName="teamId")
     */
    protected $team;
    
    /**
     * @ORM\ManyToOne(targetEntity="Season")
     * @ORM\JoinColumn(name="seasonId", referencedColumnName="seasonId")
     */
    protected $season;
}
