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


    public function findByCoordinates($data, $limit = 10000)
	{
        $q     = new Query();
        $filters  = new BoolQuery();
		$subQuery = new BoolQuery();

		foreach($data['coordinates'] as $c){
            $subMatch = new BoolQuery();

            $range = new Range('lat', [
                'lte' => $c['max']['lat'],
                'gte' => $c['min']['lat']
            ]);
            $subMatch->addMust($range);

            $range = new Range('lon', [
                'lte' => $c['max']['lon'],
                'gte' => $c['min']['lon']
            ]);
            $subMatch->addMust($range);

            $subQuery->addShould($subMatch);
		}
		$filters->addMust($subQuery);
        $q->setQuery($subQuery);

		if(isset($data['start'])){
			$q->addSort(["_geo_distance" => [
				"coordinates" => [
					'lat' => $data['start']['lat'],
					'lon' => $data['start']['lon']
				],
				"order" => "asc",
				"unit"  => "km"
			]]);
		}

        $q->setSize($limit);

        return $this->finder->find($q);
	}

	public function findByDistance($data, $limit = 10000)
	{
        $q     = new Query();
        $boolQuery = new BoolQuery();

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
                $boolQuery->addShould($subQuery);
            }
        } else {
            $boolQuery->addFilter(
                new GeoDistance('coordinates', [
                    'lat' => $data['coordinates']['lat'],
                    'lon' => $data['coordinates']['lon'],
                ], $data['radius'] . 'km'
                )
            );
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
        $q     = new Query();
        $query = new MatchAll();

        $filters = new BoolQuery();

        $query = new BoolQuery($query, $filters);

        $q->setQuery($query);

        $q->addSort(["_geo_distance" => [
            "coordinates" => [
                'lat' => $data['lat'],
                'lon' => $data['lon']
            ],
            "order"       => "asc",
            "unit"        => "km"
        ]]);

        $q->setSize($limit);

        return $this->finder->find($q);
    }
}