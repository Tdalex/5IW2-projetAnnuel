<?php

namespace AppBundle\Command;

use AppBundle\Entity\Waypoint;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Prewk\XmlStringStreamer;
use SKAgarwal\GoogleApi\PlacesApi;

class UpdateWaypointCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('roadtrip:waypoint:update')
            ->setDescription('Update waypoint from Gmap')
            ->addOption(
                'forceUpdate',
                'f',
                InputOption::VALUE_OPTIONAL,
                'force update all waypoint',
                false
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('max_execution_time', 3600);

        $force = $input->getOption('forceUpdate');
        $this->generate($output, $force);
    }

    /**
     * @param OutputInterface $output
     */
    public function generate(OutputInterface $output, $force)
    {
        $output->writeln('<info>================</info>');
        $output->writeln('<info>UPDATE WAYPOINT</info>');
        $output->writeln('<info>================</info>');
        $output->writeln('');
        $output->writeln('');

        $container  = $this->getContainer();
        $em         = $container->get('doctrine')->getManager();

        $googlePlaces = new PlacesApi($container->getParameter('google_api_key'));
        $googlePlaces->verifySSL(false);

        $start = array(
                    'lat' => 42.837042,
                    'lon' => -4.51328
                );

        $end = array(
                    'lat' => 50.73645528205696,
                    'lon' => 7.731628249999978
                );

        $count = array( 'total' => 0,
                        'update'=> 0,
                        'new'   => 0
            );

        $done = [];

        $leftSideDist = $end['lon'] - $start['lon'];
        $belowSideDist = $end['lat'] - $start['lat'];

        $cut = 20;
        $excLat = $belowSideDist / $cut;
        $excLng = $leftSideDist / $cut;

        $output->writeln('<info>Cleaning waypoints, this may take a while</info>');
        $output->writeln('');

        foreach($em->getRepository('AppBundle:Waypoint')->findAllActive() as $w){
            if(!$w->isSponsor()){
                $w->setStatus(Waypoint::STATUS_DISABLED);
                $em->persist($w);
                $em->flush();
            }
        }

        $output->writeln('<info>Getting Waypoints</info>');
        $output->writeln('');
        for($i = 0; $i < $cut; $i++){
            for($a = 0; $a < $cut; $a++){
                $search = $googlePlaces->nearbySearch(($start['lat'] + ($excLat * $i)) ."," . ($start['lon'] + ($excLng * $a)), 50000, array("type" => "lodging", "language" => "fr"));

                foreach($search['results'] as $s){

                    $name = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $s['name']);

                    if(in_array($s['id'], $done)){
                        continue;
                    }

                    $done[] = $s['id'];
                    $count['total']++;

                    $waypoint = $em->getRepository('AppBundle:Waypoint')->findOneByGoogleId($s['id']);
                    $new = false;

                    if (!$waypoint) {
                        $output->writeln('<info>Not found, creating a new waypoint</info>');
                        $output->writeln('');
                        $output->writeln('');
                        $waypoint = new waypoint();
                        $count['new']++;
                    } else {
                        $output->writeln('<info>Updating data</info>');
                        $output->writeln('');
                        $output->writeln('');
                        $count['update']++;
                    }

                    $output->writeln('<info>updating...</info>');
                    $output->writeln('');
                    $waypoint->setTitle($name);
                    $waypoint->setType($s['types']);
                    $waypoint->setGoogleId($s['id']);
                    $waypoint->setRating(isset($s['rating']) ? $s['rating'] : null);
                    $waypoint->setIcon($s['icon']);
                    $waypoint->setStatus(Waypoint::STATUS_ENABLED);
                    $waypoint->setAddress($s['vicinity']);
                    $waypoint->setLat($s['geometry']['location']['lat']);
                    $waypoint->setLon($s['geometry']['location']['lng']);

                    $em->persist($waypoint);
                    $em->flush();

                    $output->writeln('<info>updated</info>');
                    $output->writeln('');
                    $output->writeln('');
                }
            }
        }
        $output->writeln('<info>new: '.    $count['new'] .'</info>');
        $output->writeln('<info>update: '. $count['update'] .'</info>');
        $output->writeln('<info>total: '.  $count['total']  .'</info>');
        $output->writeln('');
        $output->writeln('<info>===============</info>');
        $output->writeln('<info>GENERATION DONE</info>');
        $output->writeln('<info>===============</info>');

    }
}
