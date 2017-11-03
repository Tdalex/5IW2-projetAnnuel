<?php 

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Article;
use AppBundle\DataFixtures\ORM\TagFixture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ArticleFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();
        
        $tags = $manager->getRepository('AppBundle:Tag')->findAll();
        for ($i = 0; $i < 20; $i++) {
            $article = new Article();
            $article->setIsRemoved(false);
            $article->setTags($tags[rand(0, count($tags))]);
            $article->setTitle($faker->name);
            $article->setContent($faker->text);
            $article->setCreatedAt($faker->dateTime);
            $manager->persist($article);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            TagFixture::class,
        );
    }
}