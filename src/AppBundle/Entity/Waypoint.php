<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WaypointRepository")
 * @ORM\Table(name="Waypoint")
 *
 */
class Waypoint
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
    private $theme;

	/**
	* @var string
	*
	* @ORM\Column(type="string")
	*/
    private $phone;

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
	* @ORM\Column(type="string")
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
    private $description;

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
}
