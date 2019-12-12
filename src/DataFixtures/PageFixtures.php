<?php

namespace App\DataFixtures;

use App\Content\Page;
use App\Entity\Node;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class PageFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 10; $i++) {
            $page = new Page();
            $page->setParent('pages')
                 ->setName("page_$i")
                 ->setPath("/pages/page_$i")
                 ->setLocale('en')
                 ->setTitle("The title of the page $i")
                 ->setContent("The content of the page $i")
                 ->setCreatedAt(date_create());

            $node = new Node();
            $manager->persist($page->buildNode($node));
        }

        $manager->flush();
    }
}
