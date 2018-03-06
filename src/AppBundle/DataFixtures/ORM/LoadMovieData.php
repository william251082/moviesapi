<?php
/**
 * Created by PhpStorm.
 * User: williamdelrosario
 * Date: 3/6/18
 * Time: 12:01 PM
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadMovieData extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $movie1 = new Movie();
        $movie1->setTitle('Green Mile');
        $movie1->setYear(1999);
        $movie1->setTime(   189);
        $movie1->getDescription('Just a movie description');

        $manager->persist($movie1);
        $manager->flush();
    }
}