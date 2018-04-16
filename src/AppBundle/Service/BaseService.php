<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

/**
 * Abstract class for the service which manage the Entity Manager
 *
 */
abstract class BaseService
{

    /**
     * @var EntityManager The Entity Manager
     */
    protected $em;

    /**
     * Getter of the Entity Manager
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * Setter of the Entity Manager
     *
     * @param EntityManager $em the Entity Manager
     */
    public function setEntityManager($em)
    {
        $this->em = $em;
    }

    /**
     * Add a repository to this service
     *
     * @param integer $key   Key
     * @param string  $class Class
     *
     * @return void
     */
    public function addRepository($key, $class)
    {
        $this->$key = $this->em->getRepository($class);
    }

    /**
     * Add a service to this service
     *
     * @param integer $key     Key
     * @param string  $service Class
     *
     * @return void
     */
    public function addService($key, $service)
    {
        $this->$key = $service;
    }

    /**
     * Check if a given key is defied in array and not empty
     *
     * @param mixed $key
     * @param array $haystack
     * @return boolean
     */
    public function isDefined($key, array $haystack)
    {
        return (is_array($haystack) && isset($haystack[$key]) && !empty($haystack[$key]));
    }
}
