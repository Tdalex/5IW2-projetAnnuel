<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;

/**
* @ORM\Entity(repositoryClass="AppBundle\Repository\ArticleRepository")
*/
class Article
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
     * @ORM\ManyToMany(targetEntity="Tag", cascade={"persist", "remove"})
     */
    protected $tags;
	
	/**
	* @var string
	*
	* @ORM\Column(type="boolean", options={"default" : false})
	*/
	private $isRemoved;	
	
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
	* @var DateTime
	*
	* @ORM\Column(type="datetime")
	*/
	private $createdAt;

	/**
	* @var string
	*
	* @ORM\Column(type="text")
	*/
	private $content;
	
	public function __construct(){
		$this->createdAt = new \DateTime();
	}
	
	/**
	* return int
	*/
	public function getId(){
		return $this->id;
	}
	
	/**
	* return string
	*/
	public function getTitle(){
		return $this->title;
	}

	/**
	* return string
	*/
	public function getSlug(){
		return $this->slug;
	}
	
	/**
	* return string
	*/
	public function setSlug($slug){
		return $this->slug = $slug;
	}

	/**
	* return dateTime
	*/
	public function getCreatedAt(){
		return $this->createdAt;
	}
	
	/**
	* return string
	*/
	public function getContent(){
		return $this->content;
	}
	
	/**
	* return string
	*/
	public function setTitle($title){
		return $this->title = $title;
	}
	
	/**
	* return dateTime
	*/
	public function setCreatedAt($date){
		return $this->CreatedAt = $date;
	}
	
	/**
	* return string
	*/
	public function setContent($content){
		return $this->content = $content;
	}

	/** Get tags
	*
	* @return tags
	*/
   public function getTags()
   {
	   return $this->tags;
   }

   /**
	* @param Tag $tag
	* @return $this
	*/
   public function setTags(Tag $tags)
   {
	   $this->tags[] = $tags;
	   return $this;
   }

    /**
     * Add tag
     *
     * @param \AppBundle\Entity\Tag $tag
     *
     * @return Article
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
        $this->tag->removeElement($tag);
    }

    /**
     * Set isRemoved
     *
     * @param boolean $isRemoved
     *
     * @return Article
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
}
