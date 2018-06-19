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
            'lon' => -7.731628249999978
        );

        $leftSideDist = $end['lon'] - $start['lon'];
        $belowSideDist = $end['lat'] - $start['lat'];

        $excLat = $belowSideDist / 20;
        $excLng = $leftSideDist / 20;

        for($i = 0; $i < 20; $i++){
            for($a = 0; $a < 20; $a++){
                $search = $googlePlaces->nearbySearch(($start['lat'] + ($excLat * $i)) ."," . ($start['lon'] + ($excLng * $a)), 50000, array("type" => "lodging", "language" => "fr"));

                foreach($search['results'] as $s){
                    $waypoint = $em->getRepository('AppBundle:Waypoint')->findOneByAddress($s['vicinity']);
                    $new = false;

                    if (!$waypoint) {
                        $output->writeln('<info>Not found, creating a new waypoint</info>');
                        $output->writeln('');
                        $output->writeln('');
                        $waypoint = new waypoint();
                        $new = true;
                    }else{
                        $output->writeln('<info>Already exists</info>');
                        $output->writeln('');
                        $output->writeln('');
                    }
                    if($new || $force){
                        $output->writeln('<info>updating...</info>');
                        $output->writeln('');
                        $waypoint->setTitle($s['name']);
                        $waypoint->setAddress($s['vicinity']);
                        $waypoint->setLat($s['geometry']['location']['lat']);
                        $waypoint->setLng($s['geometry']['location']['lon']);

                        $em->persist($waypoint);
                        $em->flush();

                        $output->writeln('<info>updated</info>');
                        $output->writeln('');
                        $output->writeln('');
                    }
                }
            }
        }

        $output->writeln('');
        $output->writeln('<info>===============</info>');
        $output->writeln('<info>GENERATION DONE</info>');
        $output->writeln('<info>===============</info>');

    }
}
