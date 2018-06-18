<?php

namespace AppBundle\Transformer;

use AppBundle\Service\TransformWaypointService;
use AppBundle\Entity\Waypoint;
use Elastica\Document;
use FOS\ElasticaBundle\Transformer\ModelToElasticaTransformerInterface;

class WaypointToElasticaTransformer implements ModelToElasticaTransformerInterface
{
    /**
     * @var TransformWaypointService $transformWaypointService
     */
    protected $transformWaypointService;

    public function addService($key, $service)
    {
        $this->$key = $service;
    }

    /**
     * @param Waypoint $waypoint
     * @param array $fields
     * @return Document
     */
    public function transform($waypoint, array $fields)
    {
        $identifier = $waypoint->getId();

        $data = $this->transformWaypointService->transform($waypoint);

        $document = new Document($identifier, $data);

        return $document;
    }
}
