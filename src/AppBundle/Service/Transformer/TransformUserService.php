<?php

namespace AppBundle\Service\Transformer;

use AppBundle\Service\BaseService;
use AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class TransformUserService
 *
 * @package AppBundle\Service
 */
class TransformUserService extends BaseService
{
    /**
     * @param User $user
     * @return array
     */
    public function transform(User $user)
    {
        $data = [
            "id" => $user->getId()
        ];

        return $data;
    }
}
