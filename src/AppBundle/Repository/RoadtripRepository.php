<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class RoadtripRepository extends EntityRepository{

    public function findByTag($tag, $isRemoved = false)
	{
		$query = $this->createQueryBuilder('a')
				->join('a.tags', 't')
				->where('t.slug = :tag')
				->setParameter('tag', $tag)
				->andWhere('a.isRemoved = :isRemoved')
				->setParameter('isRemoved', $isRemoved)
				->orderBy('a.title', 'ASC')
				->getQuery();

		return $query->getResult();
	}

	public function search($filters)
	{
		$query = $this->createQueryBuilder('r')
					  ->where('r.isRemoved = false');

		if(isset($filters['duration']) && !empty($filters['duration'])){
			$query->andWhere('r.duration >= :minDuration')
					->andWhere('r.duration <= :maxDuration')
					->setParameter('minDuration', $filters['duration']['min'])
					->setParameter('maxDuration', $filters['duration']['max']);
		}

		if(isset($filters['nbStops']) && !empty($filters['nbStops'])){
			$query->andWhere('r.nbStops >= :minNbStops')
					->andWhere('r.nbStops <= :maxNbStops')
					->setParameter('minNbStops', $filters['nbStops']['min'])
					->setParameter('maxNbStops', $filters['nbStops']['max']);
		}

		if(isset($filters['address']) && !empty($filters['address'])){
			$query->leftJoin('r.stops', 's')
				->andWhere('s.address like :address')
				->setParameter('address', '%'.$filters['address']. '%');
		}

		if(isset($filters['order'])){
			$query->orderBy('r.'. $filters['order'], 'ASC');
		}else{
			$query->orderBy('r.createdAt', 'DESC');
		}


		return $query->getQuery()->getResult();
	}

	public function findExtremDuration()
	{
		$query = $this->createQueryBuilder('a')
				 ->select('a.duration')
				 ->orderBy('a.duration','ASC');

		return $query->getQuery()->getResult();
	}

	public function findExtremNbStops()
	{
		$query = $this->createQueryBuilder('a')
				 ->select('a.nbStops')
				 ->orderBy('a.nbStops','ASC');

		return $query->getQuery()->getResult();
	}

	public function searchRtDepDest($dep, $dest) {
        $query = $this->createQueryBuilder('r')
                ->select('r.title', 'r.description', 'r.slug')
                ->innerJoin('r.stopStart', 'dep')
                ->innerJoin('r.stopEnd', 'dest')
                ->where('dep.address = :dep')
                ->setParameter('dep', $dep)
                ->andWhere('dest.address = :dest')
                ->setParameter('dest', $dest)
                ->getQuery()
                ->getResult();

        return $query;
	}

}
?>