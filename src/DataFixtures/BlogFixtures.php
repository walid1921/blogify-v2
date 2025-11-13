<?php

namespace App\DataFixtures;

use App\Entity\Blog;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BlogFixtures extends Fixture
{
    public function load (ObjectManager $manager): void
    {
//        $faker = Factory::create();
//
//        // Generate 100 blogs
//        for ($i = 0; $i < 100; $i++) {
//            $blog = new Blog();
//            $blog->setTitle($faker->sentence(6, true));
//            $blog->setContent($faker->paragraphs(1, true));
//            $blog->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTime()));
//            $blog->setLikes($faker->numberBetween(0, 1000));
//            $blog->setIsPublished($faker->boolean());
//
//            $manager->persist($blog);
//        }
//
//        $manager->flush();
    }
}
