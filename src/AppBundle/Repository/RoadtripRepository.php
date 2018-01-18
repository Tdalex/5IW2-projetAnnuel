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
		$query = $this->createQueryBuilder('a')
				->orderBy('a.title', 'ASC')
				->getQuery();

				return $query->getResult();
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