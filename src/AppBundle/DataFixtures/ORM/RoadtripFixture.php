<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Roadtrip;
use AppBundle\Entity\Stop;
use AppBundle\DataFixtures\ORM\TagFixture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class RoadtripFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();

        $user = $manager->getRepository('AppBundle:User')->findAll();

        for ($i = 0; $i < 50; $i++) {
            $nbStops = rand(0, 5);
            $roadtrip = new Roadtrip();

            $roadtrip->setIsRemoved(false);
            $roadtrip->setOwner($user[rand(0, count($user)-1)]);
            $roadtrip->setTitle($faker->name);
            $roadtrip->setNbStops($nbStops);
            $roadtrip->setDescription($faker->text);
            $roadtrip->setCreatedAt($faker->dateTime);
            $roadtrip->setDuration(rand(0, 100));
            $manager->persist($roadtrip);

            $idRoadtrip = $roadtrip->getId();
            $rt = $manager->getRepository('AppBundle:Roadtrip')->findOneBy(array('id' => $idRoadtrip));

            for ($a = 0; $a < $nbStops; $a++) {
                $st = new Stop();
                $st->setAddress($faker->address);
                $st->setTitle($faker->name);
                $st->setDescription($faker->text);
                $st->setLat($faker->latitude);
                $st->setlon($faker->longitude);
                $st->setRoadTripStop($rt);
                $roadtrip->addStop($st);
                $manager->persist($st);
            }

            //startStop
            $start = new Stop();
            $start->setAddress($faker->address);
            $start->setTitle($faker->name);
            $start->setDescription($faker->text);
            $start->setLat($faker->latitude);
            $start->setlon($faker->longitude);
            $start->setRoadTripStop($rt);
            $roadtrip->setStopStart($start);

            //endStop
            $end = new Stop();
            $end->setAddress($faker->address);
            $end->setTitle($faker->name);
            $end->setDescription($faker->text);
            $end->setLat($faker->latitude);
            $end->setlon($faker->longitude);
            $end->setRoadTripStop($rt);
            $roadtrip->setStopEnd($end);

            $roadtrip->getStopStart()->setRoadTripStop($rt);
            $roadtrip->getStopEnd()->setRoadTripStop($rt);
            $manager->persist($start);
            $manager->persist($end);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixture::class,
        );
    }
}