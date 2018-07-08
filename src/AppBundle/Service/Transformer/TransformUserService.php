<?php

namespace AppBundle\Service\Transformer;

use AppBundle\Service\BaseService;
use AppBundle\Entity\User;
use AppBundle\Entity\Roadtrip;
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

        $roadtrips = $user->getOwned();
        $roadtrip = array();

        if ($roadtrips) {
            foreach($roadtrips as $r){
                $roadtrip[] = array(
                    'id'          => $r->getId(),
                    'slug'        => $r->getSlug(),
                    'title'       => $r->getTitle(),
                    'description' => $r->getDescription(),
                    'duration'    => $r->getDuration(),
                    'nbStops'     => $r->getNbStops(),
                    'reviews'     => $r->getReview(),
                    'stops'       => $r->getStops(),
                    'stopStart'   => $r->getStopStart(),
                    'stopEnd'     => $r->getStopEnd(),
                );
            }
        }

        $data = [
            "id"        => $user->getId(),
            "firstname" => $user->getFirstName(),
            "lastname"  => $user->getLastName(),
            "birthdate" => $user->getBirthdate(),
            "gender"    => $user->getGender(),
            "email"     => $user->getEmail(),
            "roadtrip"  => $roadtrip
        ];

        return $data;
    }
}
