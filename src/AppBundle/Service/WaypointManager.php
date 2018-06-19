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
        $query = new MatchAll();

        $filters = new BoolQuery();
		$subQuery = new BoolQuery();
		$subMatch = new BoolQuery();

		foreach($data['coordinates'] as $c){
				$match = new BoolQuery();
				$match = new Range('coordinates.lat', [
					'lte' => $c['max']['lat'],
					'gte' => $c['min']['lat']
				]);
				$subMatch->addMust($match);

				$match = new BoolQuery();
				$match = new Range('coordinates.lng', [
					'lte' => $c['max']['lon'],
					'gte' => $c['min']['lon']
				]);
				$subMatch->addMust($match);

				$subQuery->addShould($subMatch);
		}
		$filters->addMust($subQuery);


		$query = new BoolQuery($query, $filters);

        $q->setQuery($query);

		if(isset($data['start'])){
			$q->addSort(["_geo_distance" => [
				"coordinates" => [
					'lat' => $start['lat'],
					'lon' => $start['lon']
				],
				"order"       => "asc",
				"unit"        => "km"
			]]);
		}

        $q->setSize($limit);

        return $this->finder->find($q);
	}

	public function findByDistance($data, $limit = 10000)
	{
        $q     = new Query();
        $query = new MatchAll();

		$boolQuery = new BoolQuery();
		$boolQuery->addFilter(
			new GeoDistance('coordinates', [
				'lat' => $data['coordinates']['lat'],
				'lon' => $data['coordinates']['lon'],
			], $data['distance'] . 'km'
			)
		);

		$query = new BoolQuery($query, $boolQuery);

        $q->setQuery($query);

		$q->addSort(["_geo_distance" => [
			"coordinates" => [
				'lat' => $data['coordinates']['lat'],
				'lon' => $data['coordinates']['lon'],
			],
			"order"       => "asc",
			"unit"        => "km"
		]]);

        $q->setSize($limit);

        return $this->finder->find($q);
	}

  	/**
     * @param $coordinates
     * @return array
     */
    public function findByNearestWaypoint($coordinates, $limit = 1)
    {
        $q     = new Query();
        $query = new MatchAll();

        $filters = new BoolQuery();

        $query = new BoolQuery($query, $filters);

        $q->setQuery($query);

        $q->addSort(["_geo_distance" => [
            "coordinates" => [
                'lat' => $coordinates['lat'],
                'lon' => $coordinates['lon']
            ],
            "order"       => "asc",
            "unit"        => "km"
        ]]);

        $q->setSize($limit);

        return $this->finder->find($q);
    }
}