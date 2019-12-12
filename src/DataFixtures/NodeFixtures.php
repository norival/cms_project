<?php

namespace App\DataFixtures;

use App\Entity\Node;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class NodeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $node = new Node();
        $node->setParent(null)
             ->setName('/')
             ->setPath('/')
             ->setLocale(null)
             ->setCreatedAt(date_create());
        $manager->persist($node);

        $node = new Node();
        $node->setParent('/')
             ->setName('pages')
             ->setPath('/pages')
             ->setLocale(null)
             ->setCreatedAt(date_create());
        $manager->persist($node);

        $manager->flush();
    }
}
