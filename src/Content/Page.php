<?php

namespace App\Content;

use App\Entity\Node;

class Page extends BaseNode
{
    /** @var $title */
    private $title;

    /** @var $content */
    private $content;

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
     * @Serializer\VirtualProperty()
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
     * @Serializer\VirtualProperty()
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

    public function buildNode()
    {
        // TODO: look if possible to use a trait here
        $node = new Node();

        $node->setParent($this->getParent());
        $node->setPath($this->getPath());
        $node->setName($this->getName());
        $node->setCreatedAt($this->getCreatedAt());
        $node->setUpdatedAt($this->getUpdatedAt());
        $node->setUser($this->getUser());
        $node->setType($this->getType());

        $node->setProperty('title', $this->getTitle());
        $node->setProperty('content', $this->getContent());

        return $node;
    }
}
