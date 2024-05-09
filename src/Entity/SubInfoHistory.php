<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubInfoHistory
 *
 * @ORM\Table(name="sub_info_history", indexes={@ORM\Index(name="fk_subid", columns={"subscriber_id"})})
 * @ORM\Entity
 */
class SubInfoHistory
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="weight", type="integer", nullable=false)
     */
    private $weight;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="subscriber_id", referencedColumnName="id")
     * })
     */
    private $subscriber;


}
