<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository{

    public function findOneById($id)
	{
		$query = $this->createQueryBuilder('u')
				->where('u.id = :id')
				->setParameter('id', $id)
				->getQuery();

				return $query->getResult();
	}
}
?>