<?php

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Entita payment.
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\Table(name="payment")
 */
class Payment extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="paymentId")
     * @ORM\GeneratedValue
     */
    protected $paymentId;
    
    /**
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumn(name="playerId", referencedColumnName="playerId")
     */
    protected $player;
    
    /**
     * @ORM\Column(type="string", name="purpose")
     */
    protected $purpose;
    
    /**
     * @ORM\Column(type="integer", name="sum")
     */
    protected $sum;
    
    /**
     * @ORM\Column(type="datetime", name="dueDate")
     */
    protected $dueDate;
    
    /**
     * @ORM\Column(type="datetime", name="payedOn")
     */
    protected $payedOn;
    
    /**
     * @ORM\Column(type="string", name="variableSymbol")
     */
    protected $variableSymbol;
    
    /**
     * @ORM\ManyToOne(targetEntity="Season")
     * @ORM\JoinColumn(name="seasonId", referencedColumnName="seasonId")
     */
    protected $season;
    
    /**
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumn(name="teamId", referencedColumnName="teamId")
     */
    protected $team;
}
