<?php

namespace App\Controller;

use App\Content\Page;
use App\Entity\Node;
use App\Form\PageType;
use App\Form\UpdatePageType;
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

        if (!$form->isValid()) {
            /* dump((string) $form->getErrors(true, false)); */
            /* die; */
        }

        // set the date of creation
        $page->setCreatedAt(date_create());

        // TODO set the user

        // persist and flush object
        $node = new Node();
        $node->update($page);
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
     * @Route("/pages/{name}", name="page_delete", methods={"DELETE"})
     */
    public function delete($name)
    {
        // get page by name
        $node = $this->repository->findOneBy(['name' => $name]);

        if ($node) {
            $this->em->remove($node);
            $this->em->flush();
        }

        // return SUCCESS code even if the page does not exist because
        // it means it has been successfully deleted
        return new Response(null, 204);
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

        // throw not found exception if no node with that name
        if (!$node) {
            throw $this->createNotFoundException(sprintf('No page found with name "%s"', $name));
        }

        $page = new Page();
        $page->bindNode($node);
        
        // serialize page
        $json = $this->serializer->serialize($page, 'json');

        // send response
        return new Response($json, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @Route("/pages/{name}", name="page_update", methods={"PUT", "PATCH"})
     */
    public function update(Request $request, $name)
    {
        // get page by name
        $node = $this->repository->findOneBy(['name' => $name]);

        // if no page found, throw 404 error
        if (!$node) {
            throw $this->createNotFoundException(sprintf('No page found with name "%s"', $name));
        }

        // bind data from the PUT request and submit the form
        $page = new Page();
        $page->bindNode($node);
        $form = $this->createForm(UpdatePageType::class, $page);

        /* $this->processForm($request, $form); */
        $data = json_decode($request->getContent(), true);
        $form->submit($data, $request->getMethod() != 'PATCH');

        // set the date of modification
        $page->setUpdatedAt(date_create());

        // persist and flush object
        $node->update($page);
        $this->em->persist($node);
        $this->em->flush();

        // get the newly created page
        $node = $this->repository->find($node->getId());
        
        // serialize page
        $json = $this->serializer->serialize($page->bindNode($node), 'json');

        // send a response
        return new Response($json, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
}
