<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation\Link;

/**
 * Player
 *
 * @ORM\Table(name="players", indexes={@ORM\Index(name="name", columns={"name"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlayerRepository")
 * @Link(
 *  "self",
 *  route = "get_player",
 *  params = { "id": "object.getId()" }
 * )
 */
class Player
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="id", type="smallint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=30, nullable=false)
     *
     * @Assert\NotBlank(message="A player must have a name - except for Jaqen H'ghar - who is actually No one")
     */
    private $name;


    /**
     * Set name
     *
     * @param string $name
     *
     * @return Player
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get id
     *
     * @return boolean
     */
    public function getId()
    {
        return $this->id;
    }
}
