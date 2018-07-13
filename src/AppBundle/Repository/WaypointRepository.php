<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Waypoint;

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

    public function findOneByAddressAndTitle($address, $title)
	{
		$query = $this->createQueryBuilder('w')
				->where('w.address = :address')
				->setParameter('address', $address)
				->andwhere('w.title = :title')
				->setParameter('title', $title)
				->getQuery();

		$result = $query->getResult();
		if(!empty($result)){
			return $result[0];
		}
		return null;
	}

	public function findOneByGoogleId($googleId)
	{
		$query = $this->createQueryBuilder('w')
				->where('w.googleId = :googleId')
				->setParameter('googleId', $googleId)
				->getQuery();

		$result = $query->getResult();
		if(!empty($result)){
			return $result[0];
		}
		return null;
	}

	public function findAllActive()
	{
		$query = $this->createQueryBuilder('w')
				->where('w.status = :status')
				->setParameter('status', Waypoint::STATUS_ENABLED)
				->getQuery();

		return $query->getResult();
	}
}
?>