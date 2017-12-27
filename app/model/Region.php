<?php

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Doctrine entita pro tabulku region.
 * @package App\Model
 * @ORM\Entity
 */
class Region extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="regionId")
     * @ORM\GeneratedValue
     */
    protected $regionId;

    /**
     * @ORM\Column(type="string", name="regionName")
     */
    protected $regionName;
}
