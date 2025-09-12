<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $category1 = new Category();
        $category1->setName('Travel & Adventure');
        $manager->persist($category1);
        $this->addReference('category1', $category1);

        $category2 = new Category();
        $category2->setName('Sport');
        $manager->persist($category2);
        $this->addReference('category2', $category2);

        $category3 = new Category();
        $category3->setName('Entertainment');
        $manager->persist($category3);
        $this->addReference('category3', $category3);


        $category4 = new Category();
        $category4->setName('Human Relation');
        $manager->persist($category4);
        $this->addReference('category4', $category4);

        $category5 = new Category();
        $category5->setName('Others');
        $manager->persist($category5);
        $this->addReference('category5', $category5);


        $manager->flush();
    }
}
