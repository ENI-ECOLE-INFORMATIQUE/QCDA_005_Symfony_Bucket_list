<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\User;
use App\Entity\Wish;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class WishFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $categories = $manager->getRepository(Category::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        for ($i = 0; $i <= 10; $i++) {
            $wish = new Wish();
            $wish->setTitle($faker->word);
            $wish->setAuthor($faker->randomElement($users));
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
        return [CategoryFixtures::class, UserFixtures::class];
    }
}
