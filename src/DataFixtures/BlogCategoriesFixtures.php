<?php

namespace App\DataFixtures;

use App\Entity\BlogCategories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BlogCategoriesFixtures extends Fixture
{
    public function load (ObjectManager $manager): void
    {
//        $faker = Factory::create();
//
//        // Generate 10 Categories
//        for ($i = 0; $i < 10; $i++) {
//            $category = new BlogCategories();
//
//            $category->setName($faker->word());
//            $category->setCreatedAt($faker->dateTime());
//
//            $manager->persist($category);
//        }
//
//        $manager->flush();
    }
}
