<?php 

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Roadtrip;
use AppBundle\DataFixtures\ORM\TagFixture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class RoadtripFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();
        
        $user = $manager->getRepository('AppBundle:User')->findAll();
        $stop = $manager->getRepository('AppBundle:Stop')->findAll();
        for ($i = 0; $i < 20; $i++) {
            $roadtrip = new Roadtrip();
            $roadtrip->setIsRemoved(false);
            $roadtrip->setOwner($user[rand(0, count($user)-1)]);
            $roadtrip->addStop($stop[rand(0, count($stop)-1)]);
            $roadtrip->setTitle($faker->name);
            $roadtrip->setDescription($faker->text);
            $roadtrip->setCreatedAt($faker->dateTime);
            $manager->persist($roadtrip);
        }

        $manager->flush();
    }
    
    public function getDependencies()
    {
        return array(
            UserFixture::class,
            StopFixture::class,
        );
    }
}