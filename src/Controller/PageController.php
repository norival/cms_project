<?php

namespace App\Controller;

use App\Content\Page;
use App\Form\PageType;
use App\Repository\NodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PageController extends AbstractController
{
    private $em;
    private $repository;
    private $serializer;

    public function __construct(EntityManagerInterface $em, NodeRepository $repository, SerializerInterface $serializer)
    {
        $this->em         = $em;
        $this->repository = $repository;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/pages", name="page_new", methods={"POST"})
     */
    public function new(Request $request)
    {
        // create a new Page instance
        $page = new Page();

        // bind data from the POST request and submit the form
        $form = $this->createForm(PageType::class, $page);
        /* $this->processForm($request, $form); */
        $page->setCreatedAt(\date_create());

        // decode json content and submit the form
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        // set the date of creation
        $page->setCreatedAt(date_create());

        // TODO set the user

        // persist and flush object
        $node = $page->buildNode();
        $this->em->persist($node);
        $this->em->flush();

        // get the newly creaed page
        $node = $this->repository->find($node->getId());
        
        // serialize page
        $json = $this->serializer->serialize($page->bindNode($node), 'json');

        // send response
        $response = new Response($json, 200, [
            'Content-Type' => 'application/json'
        ]);

        $pageUrl = $this->generateUrl(
            'page_get',
            ['name' => $page->getName()]
        );
        $response->headers->set('Location', $pageUrl);

        return $response;
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
