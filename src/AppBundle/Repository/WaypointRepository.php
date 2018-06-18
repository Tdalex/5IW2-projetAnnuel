<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\GeoDistance;
use Elastica\Query\Match;
use Elastica\Query\MatchAll;
use Elastica\Query\Range;
use Elastica\Query\Term;

use Elastica\Result;

class WaypointRepository extends EntityRepository{

    public function findOneByAddress($address)
	{
		$query = $this->createQueryBuilder('w')
				->where('w.address = :address')
				->setParameter('address', $address)
				->getQuery();

		$result = $query->getResult();
		if(!empty($result)){
			return $result[0];
		}
		return null;
	}

    public function findWaypointByCoordinates($coordinates, $limit = 10000, $start = null)
	{
        $q     = new Query();
        $query = new MatchAll();

        $filters = new BoolQuery();
		$subQuery = new BoolQuery();
		$subMatch = new BoolQuery();

		foreach($coordinates as $c){
				$match = new BoolQuery();
				$match = new Range('coordinates.lat', [
					'lte' => $c['max']['lat'],
					'gte' => $c['min']['lat']
				]);
				$subMatch->addMust($match);

				$match = new BoolQuery();
				$match = new Range('coordinates.lng', [
					'lte' => $c['max']['lng'],
					'gte' => $c['min']['lng']
				]);
				$subMatch->addMust($match);

				$subQuery->addShould($subMatch);
		}
		$filters->addMust($subQuery);


		$query = new BoolQuery($query, $filters);

        $q->setQuery($query);

		if($start){
			$q->addSort(["_geo_distance" => [
				"coordinates" => [
					'lat' => $start['lat'],
					'lon' => $start['lng']
				],
				"order"       => "asc",
				"unit"        => "km"
			]]);
		}

        $q->setSize($limit);

        return $this->finder->find($q);
	}

	public function findWaypointByDistance($coordinates, $distance, $limit = 10000)
	{
        $q     = new Query();
        $query = new MatchAll();

		$boolQuery = new BoolQuery();
		$boolQuery->addFilter(
			new GeoDistance('coordinates', [
				'lat' => $coordinates['lat'],
				'lon' => $coordinates['lng'],
			], $distance . 'km'
			)
		);

		$query = new BoolQuery($query, $boolQuery);

        $q->setQuery($query);

		$q->addSort(["_geo_distance" => [
			"coordinates" => [
				'lat' => $coordinates['lat'],
				'lon' => $coordinates['lng']
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
    public function findNearestWaypoint($coordinates, $limit = 1)
    {
        $q     = new Query();
        $query = new MatchAll();

        $filters = new BoolQuery();

        $query = new BoolQuery($query, $filters);

        $q->setQuery($query);

        $q->addSort(["_geo_distance" => [
            "coordinates" => [
                'lat' => $coordinates['lat'],
                'lon' => $coordinates['lng']
            ],
            "order"       => "asc",
            "unit"        => "km"
        ]]);

        $q->setSize($limit);

        return $this->finder->find($q);
    }
}
?>