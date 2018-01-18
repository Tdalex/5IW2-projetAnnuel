<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class StopRepository extends EntityRepository{

    public function searchByTerm($term) {
        $result = $this->createQueryBuilder('s')
            ->select('s.address')
            ->where('s.address LIKE :search')
            ->setParameter('search', '%'.$term.'%')
            ->distinct()
            ->getQuery()
            ->getResult();

        return $result;
    }
}
?>