<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Entity\Article;


/**
* @ORM\Entity
*/
class Tag
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
	public function setTitle($title){
		return $this->title = $title;
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
}
