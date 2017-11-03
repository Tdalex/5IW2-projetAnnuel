<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository{
    
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
}
?>