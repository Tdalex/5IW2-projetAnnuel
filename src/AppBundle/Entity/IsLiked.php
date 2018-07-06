<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IsLiked
 *
 * @ORM\Table(name="is_liked")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IsLikedRepository")
 */
class IsLiked
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Roadtrip",  cascade={"persist"})
     * @ORM\JoinColumn(name="roadtripId", referencedColumnName="id")
     */
    protected $roadtripId;

    /**
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    protected $userId;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    

    /**
     * Set roadtripId
     *
     * @param \AppBundle\Entity\Roadtrip $roadtripId
     *
     * @return IsLiked
     */
    public function setRoadtripId(\AppBundle\Entity\Roadtrip $roadtripId = null)
    {
        $this->roadtripId = $roadtripId;

        return $this;
    }

    /**
     * Get roadtripId
     *
     * @return \AppBundle\Entity\Roadtrip
     */
    public function getRoadtripId()
    {
        return $this->roadtripId;
    }

    /**
     * Set userId
     *
     * @param \AppBundle\Entity\User $userId
     *
     * @return IsLiked
     */
    public function setUserId(\AppBundle\Entity\User $userId = null)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return \AppBundle\Entity\User
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
