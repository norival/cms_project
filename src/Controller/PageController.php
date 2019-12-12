<?php

namespace App\Controller;

use App\Content\Page;
use App\Repository\NodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PageController extends AbstractController
{
    private $repository;
    private $serializer;

    public function __construct(NodeRepository $repository, SerializerInterface $serializer)
    {
        $this->repository = $repository;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/pages", name="page_list", methods="GET")
     */
    public function list()
    {
        // retrieve nodes from the database
        $nodes = $this->repository->findBy(['type' => 'page']);

        // convert every Node to a Page object
        foreach ($nodes as $key => $node) {
            $page = new Page();
            $pages[$key] = $page->bindNode($node);
        }
        
        // serialize table
        $json = $this->serializer->serialize(['pages' => $pages], 'json');

        // send response
        return new Response($json, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @Route("/pages/{name}", name="page_get", methods="GET")
     */
    public function show($name)
    {
        // retrieve nodes from the database
        $node = $this->repository->findOneBy(['type' => 'page', 'name' => $name]);

        $page = new Page();
        $page->bindNode($node);
        
        // serialize page
        $json = $this->serializer->serialize($page, 'json');

        // send response
        return new Response($json, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
}
