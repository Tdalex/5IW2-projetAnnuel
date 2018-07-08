<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="User")
 *
 * @UniqueEntity(fields={"facebookId"}, message="Compte facebook déjà lié", groups={"User"})
 * @UniqueEntity(fields={"email"}, message="Email déjà utilisé", groups={"User"})
 *
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * FacebookId
     *
     * @ORM\Column(type="bigint", nullable=true, unique=true)
     */
    protected $facebookId;

    /**
     * Firstname
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="Ce champ est requis")
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *      minMessage = "Trop court",
     *      maxMessage = "Trop long"
     * )
     */
    protected $firstName;

    /**
     * Lastname
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="Ce champ est requis")
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *      minMessage = "Trop court",
     *      maxMessage = "Trop long"
     * )
     */
    protected $lastName;

	/**
     * @ORM\OneToMany(targetEntity="Roadtrip", mappedBy="owner", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="owned", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owned;

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
     * forgotToken
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
    protected $forgotToken;

    /**
     * Gender
     *
     * @ORM\Column(type="string", length=50)
     *
     * @Assert\NotBlank(message="Ce champ est requis")
     * @Assert\Length(
     *      min = 1,
     *      max = 50,
     *      minMessage = "Trop court",
     *      maxMessage = "Trop long"
     * )
     */
    protected $gender;

    /**
     * Birthdate
     *
     * @var \DateTime $birthdate
     *
     * @ORM\Column(type="date", nullable=true)
     */
    protected $birthdate;

    /**
     * Creation
     *
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * Edit
     *
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @ORM\OneToMany(targetEntity="Review", mappedBy="userId", cascade={"persist"})
     * @ORM\JoinColumn(name="review", referencedColumnName="id")
     */
    protected $review;

    /**
     * @ORM\OneToMany(targetEntity="IsLiked", mappedBy="userId" ,cascade={"persist"})
     * @ORM\JoinColumn(name="liked", referencedColumnName="id")
     */
    protected $liked;

    public function __construct(){
		$this->created = new \DateTime();
		$this->updated = new \DateTime();
    }

    /**
     * Add favorite
     *
     * @param \AppBundle\Entity\Roadtrip $favorite
     *
     * @return User
     */
    public function addFavorite(\AppBundle\Entity\Roadtrip $favorite)
    {
        $this->favorite[] = $favorite;

        return $this;
    }

    /**
     * Remove favorite
     *
     * @param \AppBundle\Entity\Roadtrip $favorite
     */
    public function removeFavorite(\AppBundle\Entity\Roadtrip $favorite)
    {
        $this->favorite->removeElement($favorite);
    }

    /**
     * Get favorite
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFavorite()
    {
        return $this->favorite;
    }

     /**
     * Set facebookId
     *
     * @param mixed $facebookId
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
    }

    /**
     * Get facebookId
     *
     * @return mixed
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * Set firstname
     *
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * Get firstname
     *
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
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
     * Set forgotToken
     *
     * @param mixed $forgotToken
     */
    public function setForgotToken($forgotToken)
    {
        $this->forgotToken = $forgotToken;
    }

    /**
     * Get forgotToken
     *
     * @return mixed
     */
    public function getForgotToken()
    {
        return $this->forgotToken;
    }

    /**
     * Set lastname
     *
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * Get lastname
     *
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set birthdate
     *
     * @param mixed $birthdate
     */
    public function setBirthDate($birthdate)
    {
        $this->birthdate = $birthdate;
    }

    /**
     * Get birthdate
     *
     * @return mixed
     */
    public function getBirthDate()
    {
        return $this->birthdate;
    }

    /**
     * Set gender
     *
     * @param mixed $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * Get gender
     *
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param  \DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set mail
     * @param  string $email
     * @return $this|void
     */
    public function setEmail($email)
    {
        parent::setEmail($email);
        $this->setUsername($email);
    }

    /**
     * @return mixed
     */
    public function getOwned()
    {
        return $this->owned;
    }

    /**
     * @param mixed $owned
     */
    public function setOwned($owned)
    {
        $this->owned = $owned;
    }



     /**
     * Add owned
     *
     * @param \AppBundle\Entity\Roadtrip $owned
     *
     * @return Roadtrip
     */
    public function addOwned(\AppBundle\Entity\Stop $owned)
    {
        $this->owned[] = $owned;

        return $this;
    }

    /**
     * Remove owned
     *
     * @param \AppBundle\Entity\Roadtrip $owned
     */
    public function removeStop(\AppBundle\Entity\Roadtrip $owned)
    {
        $this->owned->removeElement($owned);
    }

    /**
     * Get owned
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStops()
    {
        return $this->owned;
    }

    /**
     * Remove owned
     *
     * @param \AppBundle\Entity\Roadtrip $owned
     */
    public function removeOwned(\AppBundle\Entity\Roadtrip $owned)
    {
        $this->owned->removeElement($owned);
    }

    /**
     * Add review
     *
     * @param \AppBundle\Entity\Review $review
     *
     * @return User
     */
    public function addReview(\AppBundle\Entity\Review $review)
    {
        $this->review[] = $review;

        return $this;
    }

    /**
     * Remove review
     *
     * @param \AppBundle\Entity\Review $review
     */
    public function removeReview(\AppBundle\Entity\Review $review)
    {
        $this->review->removeElement($review);
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

    /**
     * Add liked
     *
     * @param \AppBundle\Entity\IsLiked $liked
     *
     * @return User
     */
    public function addLiked(\AppBundle\Entity\IsLiked $liked)
    {
        $this->liked[] = $liked;

        return $this;
    }

    /**
     * Remove liked
     *
     * @param \AppBundle\Entity\IsLiked $liked
     */
    public function removeLiked(\AppBundle\Entity\IsLiked $liked)
    {
        $this->liked->removeElement($liked);
    }

    /**
     * Get liked
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLiked()
    {
        return $this->liked;
    }
}
