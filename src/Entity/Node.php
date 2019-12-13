<?php

namespace App\Entity;

use App\Content\BaseNode;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NodeRepository")
 */
class Node extends BaseNode
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * getProperty
     *
     * @param  string $name
     * @return
     */
    public function getProperty(string $name)
    {
        return $this->properties[$name];
    }

    /**
     * setProperty
     *
     * @param  string $name
     * @param  string $value
     * @return self
     */
    public function setProperty(string $name, string $value): self
    {
        $this->properties[$name] = $value;

        return $this;
    }

    /**
     * update
     *
     * Update the properties of the node
     *
     * @param  BaseNode $nodeData
     * @return self
     */
    public function update(BaseNode $node): self
    {
        $nodeData = $node->toArray();

        $this->setParent($nodeData['parent']);
        $this->setPath($nodeData['path']);
        $this->setName($nodeData['name']);
        $this->setCreatedAt($nodeData['created_at']);
        $this->setUpdatedAt($nodeData['updated_at']);
        $this->setUser($nodeData['user']);

        // filter to have the content of $this->properties
        $nodeData = \array_filter($nodeData, function($prop) {
            return !\property_exists(self::class, $prop);
        }, ARRAY_FILTER_USE_KEY);

        // fill the content of $this->properties
        foreach ($nodeData as $property => $value) {
            $this->setProperty($property, $value);
        }

        return $this;
    }
}
