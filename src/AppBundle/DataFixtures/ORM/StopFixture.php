<?php 

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Stop;
use AppBundle\DataFixtures\ORM\TagFixture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class StopFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 20; $i++) {
            $roadtrip = new Stop();
            $roadtrip->setTitle($faker->name);
            $roadtrip->setDescription($faker->text);
            $roadtrip->addStopNumber($i);
            $roadtrip->setLat($faker->latitude);
            $roadtrip->setLon($faker->longitude);
            $roadtrip->setAddress($faker->address);
            $manager->persist($roadtrip);
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