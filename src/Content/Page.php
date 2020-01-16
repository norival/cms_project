<?php

namespace App\Content;

use App\Entity\Node;
use Symfony\Component\Validator\Constraints as Assert;

class Page extends BaseNode
{
    /**
     * @var $title
     * @Assert\NotBlank(message="The page must have a title")
     */
    private $title;

    /** @var $content */
    private $content;

    public function __construct()
    {
        $this->setType('page');
        $this->setTitle('');
        $this->setContent('');
    }

    /**
     * setTitle
     *
     * @param  string $title
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * getTitle
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * setContent
     *
     * @param  $content
     * @return self
     */
    public function setContent($content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * getContent
     *
     * @return ?|string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * bindNode
     *
     * @param  Node $node
     * @return self
     */
    public function bindNode(Node $node): self
    {
        $this->setParent($node->getParent());
        $this->setPath($node->getPath());
        $this->setName($node->getName());
        $this->setCreatedAt($node->getCreatedAt());
        $this->setUpdatedAt($node->getUpdatedAt());
        $this->setUser($node->getUser());
        $this->setTitle($node->getProperty('title'));
        $this->setContent($node->getProperty('content'));

        return $this;
    }

    /**
     * toArray
     *
     * Export the Page data as an array
     *
     * @return array
     */
    public function toArray(): array
    {
        $nodeData['parent']     = $this->getParent();
        $nodeData['path']       = $this->getPath();
        $nodeData['name']       = $this->getName();
        $nodeData['created_at'] = $this->getCreatedAt();
        $nodeData['updated_at'] = $this->getUpdatedAt();
        $nodeData['user']       = $this->getUser();
        $nodeData['title']      = $this->getTitle();
        $nodeData['content']    = $this->getContent();

        return $nodeData;
    }

    public function buildNode($node)
    {
        // TODO: look if possible to use a trait here
        /* $node = new Node(); */

        $node->setParent($this->getParent());
        $node->setPath($this->getPath());
        $node->setName($this->getName());
        $node->setCreatedAt($this->getCreatedAt());
        $node->setUpdatedAt($this->getUpdatedAt());
        $node->setUser($this->getUser());
        $node->setType($this->getType());

        $node->setProperty('title', $this->getTitle());
        $node->setProperty('content', $this->getContent());

        // TODO: find antoher solution here, I think it is a bit dirty
        // the node is returned so I can persist it more easily
        return $node;
    }
}
