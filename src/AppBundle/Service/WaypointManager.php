<?php
namespace AppBundle\Service;

use AppBundle\Entity\Waypoint;
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

class WaypointManager
{

    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->finder = $container->get('fos_elastica.finder.app.waypoint');
    }

    /**
     * @param $coordinates1
     * @param $coordinates2
     * @return float|int
     */
    public function getDistance($coordinates1, $coordinates2)
    {
        $deltaY = $coordinates2['gpsY'] - $coordinates1['gpsY'];
        $deltaX = $coordinates2['gpsX'] - $coordinates1['gpsX'];

        $earthRadius = 6372.795477598;

        $alpha    = $deltaY / 2;
        $beta     = $deltaX / 2;
        $a        = sin(deg2rad($alpha)) * sin(deg2rad($alpha)) + cos(deg2rad($coordinates1['gpsY'])) * cos(deg2rad($coordinates2['gpsY'])) * sin(deg2rad($beta)) * sin(deg2rad($beta));
        $c        = asin(min(1, sqrt($a)));
        $distance = 2 * $earthRadius * $c;
        $distance = ceil($distance);

        return $distance;
    }

    /**
     * @return array
     */
    public function findAll()
    {
        $q     = new Query();
        $query = new MatchAll();

        $q->setQuery($query);
        $q->setSize(10000);

        return $this->finder->find($q);
    }

    /**
     * @return array
     */
    public function findAllActive()
    {
        $q       = new Query();
        $filters = new BoolQuery();

        $match = new Match();
        $match->setFieldQuery('status', Waypoint::STATUS_ENABLED);
        $match->setFieldMinimumShouldMatch('status', '100%');
        $filters->addMust($match);

        $q->setQuery($filters);
        $q->setSize(10000);

        return $this->finder->find($q);
    }

	public function findByDistance($data, $limit = 10000)
	{
        $q     = new Query();
        $boolQuery = new BoolQuery();
        $coordinatesQuery = new BoolQuery();

        $match = new Match();
        $match->setFieldQuery('status', Waypoint::STATUS_ENABLED);
        $match->setFieldMinimumShouldMatch('status', '100%');
        $boolQuery->addMust($match);


        if(!array_key_exists('lat', $data['coordinates'])){
            foreach ($data['coordinates'] as $c) {
                $subQuery = new BoolQuery();

                $subQuery->addFilter(
                    new GeoDistance('coordinates', [
                        'lat' => $c['lat'],
                        'lon' => $c['lon'],
                    ], $data['radius'] . 'km'
                    )
                );
                $coordinatesQuery->addShould($subQuery);
            }
            $boolQuery->addMust($coordinatesQuery);

        } else {
            $boolQuery->addFilter(
                new GeoDistance('coordinates', [
                    'lat' => $data['coordinates']['lat'],
                    'lon' => $data['coordinates']['lon'],
                ], $data['radius'] . 'km'
                )
            );
        }

        if (isset($data['type'])) {
            $match = new Match();
            $match->setFieldQuery('type', $data['type']);
            $match->setFieldMinimumShouldMatch('type', '100%');
            $boolQuery->addMust($match);
        }

        $q->setQuery($boolQuery);
        $q->setSize($limit);

        return $this->finder->find($q);
	}

  	/**
     * @param $coordinates
     * @return array
     */
    public function findNearest($data, $limit = 1)
    {
        $q = new Query();
        $boolQuery = new BoolQuery();

        $match = new Match();
        $match->setFieldQuery('status', Waypoint::STATUS_ENABLED);
        $match->setFieldMinimumShouldMatch('status', '100%');
        $boolQuery->addMust($match);

        $q->setQuery($boolQuery);

        $q->addSort(["_geo_distance" => [
            "coordinates" => [
                'lat' => $data['coordinates']['lat'],
                'lon' => $data['coordinates']['lon']
            ],
            "order"       => "asc",
            "unit"        => "km"
        ]]);


        $q->setSize($limit);

        return $this->finder->find($q);
    }

}