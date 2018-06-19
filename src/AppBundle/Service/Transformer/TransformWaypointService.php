<?php

namespace AppBundle\Service\Transformer;

use AppBundle\Service\BaseService;
use AppBundle\Entity\Waypoint;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class TransformWaypointService
 *
 * @package AppBundle\Service
 */
class TransformWaypointService extends BaseService
{
    /**
     * @param Waypoint $waypoint
     * @return array
     */
    public function transform(Waypoint $waypoint)
    {
        $data = [
            'id'          => $waypoint->getId(),
            'address'     => $waypoint->getAddress(),
            'name'        => $waypoint->getTitle(),
            'description' => $waypoint->getDescription(),
            // 'themes'      => $waypoint->getThemes(),
            'phone'       => $waypoint->getPhone(),
            'slug'        => $waypoint->getSlug(),
            'is_sponsor'  => $waypoint->isSponsor() ? true : false,
            'coordinates' => [
                'lat' => $waypoint->getLat(),
                'lon' => $waypoint->getLon()
            ],
        ];

        return $data;
    }
}
