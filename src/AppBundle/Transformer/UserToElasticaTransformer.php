<?php

namespace AppBundle\Transformer;

use AppBundle\Service\TransformUserService;
use AppBundle\Entity\User;
use Elastica\Document;
use FOS\ElasticaBundle\Transformer\ModelToElasticaTransformerInterface;

class UserToElasticaTransformer implements ModelToElasticaTransformerInterface
{
    /**
     * @var TransformUserService $transformUserService
     */
    protected $transformUserService;

    public function addService($key, $service)
    {
        $this->$key = $service;
    }

    /**
     * @param User $user
     * @param array $fields
     * @return Document
     */
    public function transform($user, array $fields)
    {
        $identifier = $user->getId();

        $data = $this->transformUserService->transform($user);

        $document = new Document($identifier, $data);

        return $document;
    }
}
