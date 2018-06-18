<?php

namespace AppBundle\Transformer;

use Elastica\Document;
use FOS\ElasticaBundle\Transformer\ElasticaToModelTransformerInterface;

class ElasticaToWaypointTransformer implements ElasticaToModelTransformerInterface
{
    /**
     * @param array $elasticaObjects
     * @return Document
     */
    public function transform(array $elasticaObjects)
    {
        return $elasticaObjects;
    }

    /**
     * @param array $elasticaObjects
     *
     * @return mixed
     */
    public function hybridTransform(array $elasticaObjects)
    {
        return $elasticaObjects;
    }

    /**
     * Returns the object class used by the transformer.
     *
     * @return string
     */
    public function getObjectClass()
    {

    }

    /**
     * Returns the identifier field from the options.
     *
     * @return string the identifier field
     */
    public function getIdentifierField()
    {

    }
}
