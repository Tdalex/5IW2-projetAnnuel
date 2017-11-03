<?php 

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use AppBundle\DataFixtures\ORM\TagFixture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {       
        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setUsername('admin');
        $user->setEmail('admin@email.admin');
        $user->setPlainPassword('admin');
        $user->setEnabled(true);
        $user->setRoles(array('ROLE_SUPER_ADMIN'));

        $userManager->updateUser($user, true);

        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 20; $i++) {
            $user = $userManager->createUser();
            $user->setUsername($faker->firstname);
            $user->setEmail($faker->email);
            $user->setPlainPassword('test');
            $user->setEnabled(true);
            $user->setRoles(array('ROLE_USER'));
    
            $userManager->updateUser($user, true);
        }
    }

}