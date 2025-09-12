<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Wish;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class WishFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = \Faker\Factory::create('en_EN');

        $categories = $manager->getRepository(Category::class)->findAll();



        for ($i = 0; $i <= 10; $i++) {
            $wish = new Wish();
            $wish->setTitle($faker->word);
            $wish->setAuthor($faker->name);
            $wish->setDescription($faker->realText());
            $wish->setDateCreated(\DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-3 months', 'now')));
            $wish->setPublished($faker->numberBetween(0, 1));
            $wish->setCategory($faker->randomElement($categories));
            $manager->persist($wish);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [CategoryFixtures::class];
    }
}
