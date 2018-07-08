<?php

namespace AppBundle\Listener;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class JsVarsInitializeListener
{

    /**
     * @var \stdClass
     */
    private $jsVarEnv;


    private $container;


    /**
     * @param \stdClass $jsVarEnv
     */
    public function __construct(\stdClass $jsVarEnv, Container $container)
    {
        $this->container = $container;
        $this->jsVarEnv = $jsVarEnv;
    }

    /**
     * Initialize js vars
     */
    public function onKernelController()
    {
        $this->jsVarEnv->myGlobalEnvironnementVariable = $this->container->getParameter('kernel.environment');
    }
}