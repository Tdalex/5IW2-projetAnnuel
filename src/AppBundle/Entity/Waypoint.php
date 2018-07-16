<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Misd\PhoneNumberBundle\Validator\Constraints as MisdAssert;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WaypointRepository")
 * @ORM\Table(name="Waypoint")
 *
 */
class Waypoint
{

    const STATUS_DISABLED   = 'disabled';
    const STATUS_ENABLED    = 'enabled';
    const STATUS_DELETED    = 'deleted';

	/**
	* @var integer
	*
	* @ORM\Column(name="id", type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
    private $id;

	/**
	* @var integer
	*
	* @ORM\Column(name="googleId", type="string", nullable=true)
	*/
	private $googleId;

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
	* @ORM\Column(type="string", nullable=true))
	*/
    private $theme;

	/**
	* @var string
	*
	* @ORM\Column(type="string", nullable=true))
	*/
    private $rating;

	/**
	* @var string
	*
	* @ORM\Column(type="string", nullable=true))
	*/
    private $website;

	/**
	* @var string
	*
	* @ORM\Column(type="string", nullable=true))
	*/
    private $icon;

	/**
	* @var string
	*
	* @ORM\Column(type="string", nullable=true))
     * @MisdAssert\PhoneNumber()
	*/
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true))
     */
    protected $email;

	/**
	* @var string
	*
	* @ORM\Column(type="string")
	*/
    private $address;

	/**
	* @var string
	*
	* @Gedmo\Slug(fields={"theme"})
	* @ORM\Column(type="string", nullable=true)
	*/
	private $theme_slug;

	/**
	* @var string
	*
	* @ORM\Column(type="string")
	*/
	private $title;

	/**
	* @var string
	*
	* @ORM\Column(type="boolean")
	*/
    private $sponsor = false;

    /**
     * Status
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $status = Waypoint::STATUS_ENABLED;

	/**
	* @var string
	*
	* @ORM\Column(type="float")
	*/
	private $lat;

    /**
    * @var string
    *
    * @ORM\Column(type="float")
    */
    private $lon;

    /**
    * @var string
    *
    * @ORM\Column(type="array")
    */
    private $type;

	/**
	* @var string
	*
	* @ORM\Column(type="string", nullable=true))
	*/
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="Stop", mappedBy="waypointStop", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="stops", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $stops;

    /**
     * token
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *      minMessage = "Trop court",
     *      maxMessage = "Trop long"
     * )
     */
    protected $token;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @return Waypoint
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
     * @return Waypoint
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
     * @return Waypoint
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * Set type
     *
     * @param string $type
     *
     * @return Waypoint
     */
    public function setType($type)
    {
        $this->type = $type;

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


//    /**
//     * Set theme
//     *
//     * @param \theme $theme
//     *
//     * @return Waypoint
//     */
//    public function setTheme(\theme $theme)
//    {
//        $this->theme = $theme;
//
//        return $this;
//    }

//    /**
//     * Get theme
//     *
//     * @return \theme
//     */
//    public function getTheme()
//    {
//        return $this->theme;
//    }
//
//    /**
//     * Set themeSlug
//     *
//     * @param \theme_slug $themeSlug
//     *
//     * @return Waypoint
//     */
//    public function setThemeSlug(\theme_slug $themeSlug)
//    {
//        $this->theme_slug = $themeSlug;
//
//        return $this;
//    }
//
//    /**
//     * Get themeSlug
//     *
//     * @return \theme_slug
//     */
//    public function getThemeSlug()
//    {
//        return $this->theme_slug;
//    }

    /**
     * Set lat
     *
     * @param string $lat
     *
     * @return Waypoint
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return string
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lon
     *
     * @param string $lon
     *
     * @return Waypoint
     */
    public function setLon($lon)
    {
        $this->lon = $lon;

        return $this;
    }

    /**
     * Get lon
     *
     * @return string
     */
    public function getLon()
    {
        return $this->lon;
    }

    /**
     * Set address
     *
     * @param array $address
     *
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
     * Set phone
     *
     * @param array $phone
     *
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return array
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param string $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return boolean
     */
    public function getSponsor()
    {
        return $this->sponsor;
    }

     /**
     * Set sponsor
     *
     * @param boolean $sponsor
     *
     */
    public function setSponsor($sponsor)
    {
        $this->sponsor = $sponsor;

        return $this;
    }

    /**
     * Get sponsor
     *
     * @return boolean
     */
    public function isSponsor()
    {
        return $this->sponsor;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
   /**
    * Get active
    *
    * @return array
    */
   public function isActive()
   {
        return $this->status == Waypoint::STATUS_ENABLED ? true : false;
   }

    /**
     * Set token
     *
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Get token
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set googleId
     *
     * @param mixed $googleId
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;
    }

    /**
     * Get googleId
     *
     * @return mixed
     */
    public function getGoogleId()
    {
        return $this->googleId;
    }


    /**
     * Add stop
     *
     * @param \AppBundle\Entity\Stop $stop
     *
     * @return Waypoint
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
}
