<?php
namespace AppBundle\Service;

use AppBundle\Entity\Roadtrip;
use AppBundle\Entity\Stop;

class RoadtripManager
{
    /**
     * @return array filters
     */
    public function getFilters($em){
        $filters = array();
        // $em = $this->getDoctrine()->getManager();

        $durations = $em->getRepository('AppBundle:Roadtrip')->findExtremDuration();
        $nbStops   = $em->getRepository('AppBundle:Roadtrip')->findExtremNbStops();

        if($durations){
            $filters['durations'] = array(  'min' => $durations[0]['duration'],
                                            'max' => end($durations)['duration']);
        }

        if($nbStops){
            $filters['nbStops'] = array(    'min' => $nbStops[0]['nbStops']-2,
                                            'max' => end($nbStops)['nbStops']-2);
        }
        return $filters;
    }
}