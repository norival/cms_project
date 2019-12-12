<?php

namespace App\DataFixtures;

use App\Entity\Node;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class NodeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 10; $i++) {
            $node = new Node();
            $node->setParent('/')
                 ->setName("page_$i")
                 ->setPath("/pages/page_$i")
                 ->setLocale('en')
                 ->setCreatedAt(date_create());

            $manager->persist($page->buildNode());
        }

        $manager->flush();
    }
}
