<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class WaypointRepository extends EntityRepository{

    public function findOneByAddress($address)
	{
		$query = $this->createQueryBuilder('w')
				->where('w.address = :address')
				->setParameter('address', $address)
				->getQuery();

				return $query->getResult();
	}
}
?>