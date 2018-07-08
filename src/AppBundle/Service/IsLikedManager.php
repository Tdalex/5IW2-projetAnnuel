<?php
namespace AppBundle\Service;

use AppBundle\Entity\IsLiked;
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

class IsLikedManager
{

    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        //$this->finder = $container->get('fos_elastica.finder.app.isliked');
    }

    public function isLike($roadtrip, $userId){
        $like = $this->em->getRepository('AppBundle:IsLiked')->isLike($roadtrip, $userId);
    }

    public function showFavoriteLimited($userId, $limit = 6, $offset = 0) {
        $like = $this->em->getRepository('AppBundle:IsLiked')->showFavoriteLimited($userId, $limit, $offset);
    }
}