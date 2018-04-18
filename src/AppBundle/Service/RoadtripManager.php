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

        $filters['durations'] = array(  'min' => $durations[0]['duration'] ? $durations[0]['duration'] : 0,
                                        'max' => end($durations)['duration' ? end($durations)['duration'] : 0]);

        $filters['nbStops'] = array(    'min' => $nbStops[0]['nbStops'] ? $nbStops[0]['nbStops'] : 0,
                                        'max' => end($nbStops)['nbStops'] ? end($nbStops)['nbStops'] : 0);
        return $filters;
    }
}