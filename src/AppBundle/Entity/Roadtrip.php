<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Entity\Article;
use AppBundle\Entity\Tag;


/**
* @ORM\Entity(repositoryClass="AppBundle\Repository\RoadtripRepository")
*/
class Roadtrip
{
	/**
	* @var integer
	*
	* @ORM\Column(name="id", type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	private $id;

	/**
	* @var string
	*
	* @Gedmo\Slug(fields={"title"})
	* @ORM\Column(type="string")
	*/
	private $slug;

	/**
	* @var string
	*
	* @ORM\Column(type="string")
	*/
	private $title;

	/**
	* @var string
	*
	* @ORM\Column(type="string")
	*/
	private $description;

	/**
	* @var DateTime
	*
	* @ORM\Column(type="datetime")
	*/
	private $createdAt;

	/**
	* @var string
	*
	* @ORM\Column(type="boolean", options={"default" : false})
	*/
	private $isRemoved;

	/**
     * @ORM\OneToMany(targetEntity="Stop", mappedBy="roadTripStop", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="stops", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $stops;

    /**
     * @ORM\OneToOne(targetEntity="Stop", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="stopStart", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $stopStart;

    /**
     * @ORM\OneToOne(targetEntity="Stop", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="stopEnd", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $stopEnd;

	/**
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
     * @ORM\JoinColumn(name="owner", referencedColumnName="id")
     */
    protected $owner;

	public function __construct(){
		$this->createdAt = new \DateTime();
    }

    /**
    * @ORM\ManyToMany(targetEntity="Tag", cascade={"persist", "remove"})
    */
    protected $tags;

    /**
     * @var integer
     *
     * @ORM\Column(name="duration", type="integer")
     */
    private $duration;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbStops", type="integer")
     */
    private $nbStops;

    /**
     * @ORM\OneToMany(targetEntity="Review", mappedBy="roadtripId", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="review", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $review;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Roadtrip
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Roadtrip
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Roadtrip
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Roadtrip
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set isRemoved
     *
     * @param boolean $isRemoved
     *
     * @return Roadtrip
     */
    public function setIsRemoved($isRemoved)
    {
        $this->isRemoved = $isRemoved;

        return $this;
    }

    /**
     * Get isRemoved
     *
     * @return boolean
     */
    public function getIsRemoved()
    {
        return $this->isRemoved;
    }

    /**
     * Set lat
     *
     * @param array $lat
     *
     * @return Roadtrip
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return array
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lon
     *
     * @param array $lon
     *
     * @return Roadtrip
     */
    public function setlon($lon)
    {
        $this->lon = $lon;

        return $this;
    }

    /**
     * Get lon
     *
     * @return array
     */
    public function getlon()
    {
        return $this->lon;
    }

    /**
     * Set owner
     *
     * @param \AppBundle\Entity\User $owner
     *
     * @return Roadtrip
     */
    public function setOwner(\AppBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \AppBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set address
     *
     * @param array $address
     *
     * @return Roadtrip
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return array
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Add stop
     *
     * @param \AppBundle\Entity\Stop $stop
     *
     * @return Roadtrip
     */
    public function addStop(\AppBundle\Entity\Stop $stop)
    {
        $this->stops[] = $stop;

        return $this;
    }

    /**
     * Remove stop
     *
     * @param \AppBundle\Entity\Stop $stop
     */
    public function removeStop(\AppBundle\Entity\Stop $stop)
    {
        $this->stops->removeElement($stop);
    }

    /**
     * Get stops
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStops()
    {
        return $this->stops;
    }

    /**
     * Add tag
     *
     * @param \AppBundle\Entity\Tag $tag
     *
     * @return Roadtrip
     */
    public function addTag(\AppBundle\Entity\Tag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tag
     *
     * @param \AppBundle\Entity\Tag $tag
     */
    public function removeTag(\AppBundle\Entity\Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return mixed
     */
    public function getStopStart()
    {
        return $this->stopStart;
    }

    /**
     * @param mixed $stopStart
     */
    public function setStopStart($stopStart)
    {
        $this->stopStart = $stopStart;
    }

    /**
     * @return mixed
     */
    public function getStopEnd()
    {
        return $this->stopEnd;
    }

    /**
     * @param mixed $stopEnd
     */
    public function setStopEnd($stopEnd)
    {
        $this->stopEnd = $stopEnd;
    }

    /**
     * @return mixed
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param mixed $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return mixed
     */
    public function getNbStops()
    {
        return $this->nbStops;
    }

    /**
     * @param mixed $nbStops
     */
    public function setNbStops($nbStops)
    {
        $this->nbStops = $nbStops;
    }


    /**
     * Add avi
     *
     * @param \AppBundle\Entity\Review $avi
     *
     * @return Roadtrip
     */
    public function addAvi(\AppBundle\Entity\Review $avi)
    {
        $this->review[] = $avi;

        return $this;
    }

    /**
     * Remove avi
     *
     * @param \AppBundle\Entity\Review $avi
     */
    public function removeAvi(\AppBundle\Entity\Review $avi)
    {
        $this->review->removeElement($avi);
    }

    /**
     * Get review
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReview()
    {
        return $this->review;
    }
}
