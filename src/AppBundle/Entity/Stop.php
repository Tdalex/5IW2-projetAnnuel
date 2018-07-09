<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Entity\Article;
use AppBundle\Entity\Waypoint;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StopRepository")
 */
class Stop
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
	* @ORM\Column(type="string", nullable=true)
	*/
	private $title;

	/**
	* @var string
	*
	* @ORM\Column(type="string", nullable=true)
	*/
	private $description;

	/**
	* @var string
	*
	* @ORM\Column(type="string")
	*/
	private $lat;

	/**
	* @var string
	*
	* @ORM\Column(type="string")
	*/
	private $lon;

	/**
	* @var string
	*
	* @ORM\Column(type="string")
	*/
	private $address;

    /**
     * @ORM\ManyToOne(targetEntity="Waypoint", inversedBy="stops")
     * @ORM\JoinColumn(name="waypointStop", referencedColumnName="id")
     */
    protected $waypoint;

	/**
     * @ORM\ManyToOne(targetEntity="Roadtrip", inversedBy="stops")
     * @ORM\JoinColumn(name="roadTripStop", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $roadTripStop;

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
     * Set waypointStop
     *
     * @param \AppBundle\Entity\Waypoint $waypointStop
     *
     * @return Stop
     */
    public function setWaypointStop(\AppBundle\Entity\Roadtrip $waypointStop = null)
    {
        $this->waypointStop = $waypointStop;

        return $this;
    }

    /**
     * Get waypointStop
     *
     * @return \AppBundle\Entity\Waypoint
     */
    public function getWaypointStop()
    {
        return $this->waypointStop;
    }

    /**
     * Set roadTripStop
     *
     * @param \AppBundle\Entity\Roadtrip $roadTripStop
     *
     * @return Stop
     */
    public function setRoadTripStop(\AppBundle\Entity\Roadtrip $roadTripStop = null)
    {
        $this->roadTripStop = $roadTripStop;

        return $this;
    }

    /**
     * Get roadTripStop
     *
     * @return \AppBundle\Entity\Roadtrip
     */
    public function getRoadTripStop()
    {
        return $this->roadTripStop;
    }
}
