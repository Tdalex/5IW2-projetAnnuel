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
            ->setDescription('Update waypoint from Gmap');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('max_execution_time', 3600);
        $this->generate($output);
    }

    /**
     * @param OutputInterface $output
     */
    public function generate(OutputInterface $output)
    {
        $output->writeln('<info>================</info>');
        $output->writeln('<info>UPDATE WAYPOINT</info>');
        $output->writeln('<info>================</info>');
        $output->writeln('');
        $output->writeln('');

        $container = $this->getContainer();

        $googlePlaces = new PlacesApi($container->getParameter('google_api_key'));
        $googlePlaces->verifySSL(false);
        $search = $googlePlaces->nearbySearch("46.227638,2.213749000000007", 50000, array("type" =>"lodging"));
var_dump($search);die();


        foreach($search as $s){
            $waypoint = $container->get('AppBundle:Waypoint')->findOneByRef();

            if (!$waypoint) {
                $waypoint = new waypoint();
                // $waypoint->setTitle();
                // $waypoint->setTheme();
                $container->get('AppBundle:Waypoint')->save($waypoint);

                $output->writeln('<error>updated</error>');
                $output->writeln('');
                $output->writeln('');
            }else{
                $output->writeln('<error>Already exists !</error>');
                $output->writeln('');
                $output->writeln('');
            }
        }

        $output->writeln('');
        $output->writeln('<info>===============</info>');
        $output->writeln('<info>GENERATION DONE</info>');
        $output->writeln('<info>===============</info>');

    }
}
