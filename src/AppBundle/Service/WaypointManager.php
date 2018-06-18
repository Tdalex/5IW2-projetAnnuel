<?php
namespace AppBundle\Service;

use AppBundle\Entity\Waypoint;

class WaypointManager
{

    /**
     * @param $coordinates1
     * @param $coordinates2
     * @return float|int
     */
    public function getDistance($coordinates1, $coordinates2)
    {
        $deltaY = $coordinates2['gpsY'] - $coordinates1['gpsY'];
        $deltaX = $coordinates2['gpsX'] - $coordinates1['gpsX'];

        $earthRadius = 6372.795477598;

        $alpha    = $deltaY / 2;
        $beta     = $deltaX / 2;
        $a        = sin(deg2rad($alpha)) * sin(deg2rad($alpha)) + cos(deg2rad($coordinates1['gpsY'])) * cos(deg2rad($coordinates2['gpsY'])) * sin(deg2rad($beta)) * sin(deg2rad($beta));
        $c        = asin(min(1, sqrt($a)));
        $distance = 2 * $earthRadius * $c;
        $distance = ceil($distance);

        return $distance;
    }
}