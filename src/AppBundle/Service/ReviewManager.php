<?php
namespace AppBundle\Service;

use AppBundle\Entity\Review;
use Doctrine\ORM\EntityManager;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\GeoDistance;
use Elastica\Query\Match;
use Elastica\Query\MatchAll;
use Elastica\Query\Range;
use Elastica\Query\Term;

use Elastica\Result;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ReviewManager
{

    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        //$this->finder = $container->get('fos_elastica.finder.app.review');
    }

    public function alreadyCommented($roadtrip, $userId){
        $like = $this->em->getRepository('AppBundle:Review')->alreadyCommented($roadtrip, $userId);
    }

    public function findAllByDate($roadtrip){
        $like = $this->em->getRepository('AppBundle:Review')->findAllByDate($roadtrip);
    }
}