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
}
